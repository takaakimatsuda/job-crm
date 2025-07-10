<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Company;

class DashboardController extends Controller
{
    public function index()
    {
        $statusCounts = Company::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return Inertia::render('Dashboard', [
            'statusCounts' => $statusCounts,
        ]);
    }
}
