<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGptService
{
    public function summarize(string $memo): string
    {
        try {
            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'あなたは優秀なキャリアアドバイザーです。以下のやり取り内容をもとに、1.要約（1文）、2.次にやるべきアクションを1文でアドバイスしてください。'
                        ],
                        ['role' => 'user', 'content' => $memo],
                    ],
                ]);

            $json = $response->json();

            // レスポンスログ（確認用）
            Log::info('OpenAI GPT response', $json);

            // 成功時のコンテンツ取得
            if (isset($json['choices'][0]['message']['content'])) {
                return $json['choices'][0]['message']['content'];
            }

            // エラーレスポンスがある場合
            if (isset($json['error']['message'])) {
                return 'OpenAIエラー: ' . $json['error']['message'];
            }

            return 'GPT応答の解析に失敗しました。';
        } catch (\Exception $e) {
            Log::error('ChatGptService エラー: ' . $e->getMessage());
            return '要約中にエラーが発生しました。';
        }
    }
}
