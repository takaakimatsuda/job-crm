<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatGptService
{
    /**
     * 既存：メモ1件を要約（＋1文アクション）
     */
    public function summarize(string $memo): string
    {
        try {
            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'あなたは優秀なキャリアアドバイザーです。以下のやり取り内容をもとに、1.要約（1文）、2.次にやるべき具体的なアクション（1文）を日本語で出力してください。'
                        ],
                        ['role' => 'user', 'content' => $memo],
                    ],
                ]);

            $json = $response->json();
            Log::info('OpenAI GPT response (summarize)', $json ?? []);

            if (isset($json['choices'][0]['message']['content'])) {
                return $json['choices'][0]['message']['content'];
            }
            if (isset($json['error']['message'])) {
                return 'OpenAIエラー: ' . $json['error']['message'];
            }

            return 'GPT応答の解析に失敗しました。';
        } catch (\Exception $e) {
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

            // 履歴（新しい順に最大5件）を要約入力向けに整形
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

            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'あなたは転職支援のプロフェッショナルです。曖昧表現を避け、実行可能で具体的な指示を簡潔に提案してください。'
                        ],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                ]);

            $json = $response->json();
            Log::info('OpenAI GPT response (adviseForCompany)', $json ?? []);

            if (isset($json['choices'][0]['message']['content'])) {
                return $json['choices'][0]['message']['content'];
            }
            if (isset($json['error']['message'])) {
                return 'OpenAIエラー: ' . $json['error']['message'];
            }

            return 'GPT応答の解析に失敗しました。';
        } catch (\Exception $e) {
            Log::error('ChatGptService adviseForCompany エラー: ' . $e->getMessage());
            return 'AI提案生成中にエラーが発生しました。';
        }
    }
}
