<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class ChatGptService
{
    /** OpenAI呼び出し（成功時のみ Response を返す／失敗は例外） */
    private function callOpenAi(array $payload): Response
    {
        $key = config('services.openai.key');
        if (blank($key)) {
            // 仕様：APIキー未設定は 503 扱い
            throw new ServiceUnavailableHttpException(null, 'OpenAIのAPIキーが設定されていません。');
        }

        $res = Http::withToken($key)
            ->timeout(20)
            ->retry(1, 200)   // 軽いリトライ
            ->acceptJson()
            ->asJson()
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        // レート制限（429）はリトライ秒を付けて例外化
        if ($res->status() === 429) {
            $retry = (int) ($res->header('Retry-After') ?: 60);
            throw new TooManyRequestsHttpException($retry, 'Rate limited');
        }

        // その他の失敗はメッセージ整形して例外化（コントローラでJSON化）
        if ($res->failed()) {
            $msg = $this->messageFromOpenAiFailure($res);
            throw new \RuntimeException($msg, $res->status() ?: 500);
        }

        return $res; // 成功のみ
    }

    /** 既存：メモ1件を要約（＋1文アクション） */
    public function summarize(string $memo): string
    {
        $res = $this->callOpenAi([
            'model' => config('services.openai.model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'あなたは優秀なキャリアアドバイザーです。以下のやり取り内容をもとに、1.要約（1文）、2.次にやるべき具体的なアクション（1文）を日本語で出力してください。'
                ],
                ['role' => 'user', 'content' => $memo],
            ],
        ]);

        $json = $res->json();
        Log::info('OpenAI GPT response (summarize)', $json ?? []);

        if ($text = data_get($json, 'choices.0.message.content')) {
            return trim($text);
        }
        if ($err = data_get($json, 'error.message')) {
            return 'OpenAIエラー: ' . $err;
        }
        return 'GPT応答の解析に失敗しました。';
    }

    /** 追加：会社情報＋直近の履歴から提案 */
    public function adviseForCompany(Company $company, Collection $interactions): string
    {
        $tags = method_exists($company, 'tags') ? $company->tags->pluck('name')->implode(', ') : '';
        $context = [
            "会社名: " . ($company->name ?? '不明'),
            "ステータス: " . ($company->status ?? '未設定'),
            "希望度: " . ($company->hope_level ?? '未設定') . "（1-5）",
            "担当者: " . ($company->contact_person ?? '未設定'),
            "タグ: " . ($tags ?: 'なし'),
        ];

        $historyLines = $interactions
            ->sortByDesc('interaction_date')
            ->take(5)
            ->map(function ($i) {
                $date = (string)($i->interaction_date ?? '');
                $type = (string)($i->type ?? '');
                $memo = Str::limit((string)($i->memo ?? ''), 200);
                return "- {$date} [{$type}] {$memo}";
            })
            ->implode("\n");

        $userPrompt = <<<PROMPT
以下の会社情報と最近のやり取りを踏まえて、**日本語**で次の形式で出力してください。

# 出力形式
1. 要約（2〜3文）
2. 推奨アクション（具体的に3件。誰が・何を・いつまでに を含める。例: 「担当（あなた）→ 8/10までに◯◯求人3件を候補者へ送付」）

# 会社情報
{context}

# 最近のやり取り（新しい順・最大5件）
{history}
PROMPT;

        $userPrompt = str_replace('{context}', implode("\n", $context), $userPrompt);
        $userPrompt = str_replace('{history}', $historyLines ?: '（履歴なし）', $userPrompt);

        $res = $this->callOpenAi([
            'model' => config('services.openai.model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'あなたは転職支援のプロフェッショナルです。曖昧表現を避け、実行可能で具体的な指示を簡潔に提案してください。'
                ],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        $json = $res->json();
        Log::info('OpenAI GPT response (adviseForCompany)', $json ?? []);

        if ($text = data_get($json, 'choices.0.message.content')) {
            return trim($text);
        }
        if ($err = data_get($json, 'error.message')) {
            return 'OpenAIエラー: ' . $err;
        }
        return 'GPT応答の解析に失敗しました。';
    }

    /** OpenAI失敗レスポンスを人間向けメッセージへ */
    private function messageFromOpenAiFailure(Response $res): string
    {
        $status = (int) $res->status();
        $code   = data_get($res->json(), 'error.code');

        if ($status === 401 || $status === 403) {
            return '認証に失敗しました。APIキーを確認してください。';
        }
        if ($status === 429 || $code === 'rate_limit_exceeded') {
            return '短時間にリクエストが集中しています。しばらくしてから再試行してください。';
        }
        if ($code === 'insufficient_quota') {
            return '利用上限に達しました。管理者に上限の調整を依頼してください。';
        }
        if ($status >= 500) {
            return 'AIサービスが一時的に不安定です。時間をおいて再試行してください。';
        }
        return 'AI提案の生成に失敗しました。時間をおいて再試行してください。';
    }
}
