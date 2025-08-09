<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatGptService
{
    /** 共通：OpenAI呼び出し */
    private function callOpenAi(array $payload)
    {
        $key = config('services.openai.key');
        if (blank($key)) {
            // サーバ側で早期終了（UIでモーダル誘導しやすい固定文言）
            return response()->json(['message' => 'OPENAI_API_KEYが未設定です。環境変数を設定してください。'], 422);
        }

        $res = Http::withToken($key)
            ->timeout(20)
            ->retry(1, 200) // 軽いリトライ1回だけ
            ->acceptJson()
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        // 失敗時はここでメッセージ化（未使用警告の解消ポイント）
        if ($res->failed()) {
            $msg = $this->handleOpenAiError($res);
            // 429ならRetry-Afterヘッダをログ＆付加
            $retry = $res->header('Retry-After');
            if ($res->status() === 429 && $retry) {
                $msg .= "（約{$retry}秒後に再試行可）";
            }
            return response()->json(['message' => $msg], $res->status() ?: 400)
                ->withHeaders($retry ? ['Retry-After' => $retry] : []);
        }

        return $res;
    }

    /**
     * 既存：メモ1件を要約（＋1文アクション）
     */
    public function summarize(string $memo): string
    {
        try {
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

            // 失敗時は callOpenAi が JSONレスポンス返すので拾ってそのまま文字列返却
            if ($res instanceof \Illuminate\Http\Client\Response === false) {
                // 念のため
                return 'AIサービスに接続できませんでした。';
            }
            if ($res->failed()) {
                return data_get($res->json(), 'message', '要約中にエラーが発生しました。');
            }

            $json = $res->json();
            Log::info('OpenAI GPT response (summarize)', $json ?? []);

            if ($text = data_get($json, 'choices.0.message.content')) {
                return trim($text);
            }
            if ($err = data_get($json, 'error.message')) {
                return 'OpenAIエラー: ' . $err;
            }
            return 'GPT応答の解析に失敗しました。';
        } catch (\Throwable $e) {
            Log::error('ChatGptService summarize エラー: ' . $e->getMessage());
            return '要約中にエラーが発生しました。';
        }
    }

    /**
     * 追加：会社情報＋直近の履歴から「要約＋具体アクション（3件）」を提案
     */
    public function adviseForCompany(Company $company, Collection $interactions): string
    {
        try {
            // 会社の基本文脈を組み立て
            $tags = method_exists($company, 'tags') ? $company->tags->pluck('name')->implode(', ') : '';
            $context = [
                "会社名: " . ($company->name ?? '不明'),
                "ステータス: " . ($company->status ?? '未設定'),
                "希望度: " . ($company->hope_level ?? '未設定') . "（1-5）",
                "担当者: " . ($company->contact_person ?? '未設定'),
                "タグ: " . ($tags ?: 'なし'),
            ];

            // 履歴（新しい順に最大5件）
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

            if ($res instanceof \Illuminate\Http\Client\Response === false) {
                return 'AIサービスに接続できませんでした。';
            }
            if ($res->failed()) {
                return data_get($res->json(), 'message', 'AI提案生成中にエラーが発生しました。');
            }

            $json = $res->json();
            Log::info('OpenAI GPT response (adviseForCompany)', $json ?? []);

            if ($text = data_get($json, 'choices.0.message.content')) {
                return trim($text);
            }
            if ($err = data_get($json, 'error.message')) {
                return 'OpenAIエラー: ' . $err;
            }
            return 'GPT応答の解析に失敗しました。';
        } catch (\Throwable $e) {
            Log::error('ChatGptService adviseForCompany エラー: ' . $e->getMessage());
            return 'AI提案生成中にエラーが発生しました。';
        }
    }

    /** ★ここを実際に使用（未使用警告の解消） */
    private function handleOpenAiError($res): string
    {
        $status = (int) $res->status();
        $code   = data_get($res->json(), 'error.code');

        if ($status === 422) {
            return 'OPENAI_API_KEYが未設定です。環境変数を設定してください。';
        }
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
