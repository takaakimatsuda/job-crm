<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ChatGptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class CompanyAiController extends Controller
{
    public function __construct(private ChatGptService $chatGptService) {}

    public function advise(Company $company): RedirectResponse
    {
        // 認可を使う場合（任意）
        // $this->authorize('view', $company);

        try {
            $interactions = $company->interactions()->latest()->take(5)->get();
            $advice = $this->chatGptService->adviseForCompany($company, $interactions);

            session()->flash('ai_advice', $advice);
        } catch (\Throwable $e) {
            Log::error('AI提案失敗: ' . $e->getMessage(), ['company_id' => $company->id]);
            session()->flash('ai_advice', 'AI提案の生成に失敗しました。時間をおいて再試行してください。');
        }

        return back();
    }
}
