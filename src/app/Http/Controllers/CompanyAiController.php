<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ChatGptService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class CompanyAiController extends Controller
{
    public function __construct(private ChatGptService $chatGptService) {}

    /** JSON要求ならJSON、そうでなければ従来どおりback() */
    public function advise(Request $request, Company $company): JsonResponse|RedirectResponse
    {
        // $this->authorize('view', $company); // 任意

        // 早期ガード：キー未設定
        if (empty(config('services.openai.key'))) {
            $json = response()->json([
                'ok'      => false,
                'code'    => 'OPENAI_API_KEY_MISSING',
                'message' => 'OpenAIのAPIキーが設定されていません。',
            ], 503);

            return $request->expectsJson()
                ? $json
                : back()->with('ai_advice', 'OpenAIのAPIキーが未設定です。');
        }

        try {
            $interactions = $company->interactions()->latest()->take(5)->get();
            $advice = $this->chatGptService->adviseForCompany($company, $interactions);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok'   => true,
                    'data' => ['advice' => $advice],
                ], 200);
            }

            session()->flash('ai_advice', $advice);
            return back();
        } catch (\Throwable $e) {
            Log::error('AI提案失敗: ' . $e->getMessage(), ['company_id' => $company->id]);

            if ($request->expectsJson()) {
                return $this->handleOpenAiError($e);
            }

            session()->flash('ai_advice', 'AI提案の生成に失敗しました。時間をおいて再試行してください。');
            return back();
        }
    }

    /** エラーを必ずJSONで整形（429はRetry-After付き） */
    protected function handleOpenAiError(\Throwable $e): JsonResponse
    {
        // APIキー未設定（保険）
        if (empty(config('services.openai.key'))) {
            return response()->json([
                'ok'      => false,
                'code'    => 'OPENAI_API_KEY_MISSING',
                'message' => 'OpenAIのAPIキーが設定されていません。',
            ], 503);
        }

        // レート制限（429）
        if ($e instanceof TooManyRequestsHttpException) {
            $retry = (int)($e->getHeaders()['Retry-After'] ?? 60);

            return response()
                ->json([
                    'ok'          => false,
                    'code'        => 'RATE_LIMITED',
                    'message'     => 'リクエストが多すぎます。しばらく待って再試行してください。',
                    'retry_after' => $retry,
                ], 429)
                ->withHeaders(['Retry-After' => $retry]);
        }

        // サービス停止・外部要因（503）
        if ($e instanceof ServiceUnavailableHttpException) {
            return response()->json([
                'ok'      => false,
                'code'    => 'OPENAI_ERROR',
                'message' => $e->getMessage() ?: 'Service unavailable',
            ], 503);
        }

        // 想定外は500
        return response()->json([
            'ok'      => false,
            'code'    => 'OPENAI_ERROR',
            'message' => 'AI処理中にエラーが発生しました。',
        ], 500);
    }
}
