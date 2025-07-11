<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Company;
use App\Models\Interaction;

class DashboardController extends Controller
{
    public function index()
    {
        // ステータスごとの件数取得
        $statusCounts = Company::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // 最近の履歴（5件）
        $recentUpdates = Interaction::with('company')
            ->orderBy('interaction_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($interaction) {
                return [
                    'id' => $interaction->id,
                    'date' => $interaction->interaction_date->format('Y-m-d'),
                    'company_name' => $interaction->company->name,
                    'type' => $interaction->type,
                    'memo' => $interaction->memo,
                ];
            });

        return Inertia::render('Dashboard', [
            'statusCounts' => $statusCounts,
            'recentUpdates' => $recentUpdates,
        ]);
    }
}
