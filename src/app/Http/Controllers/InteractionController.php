<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            'interaction_date' => 'required|date',
            'type'             => 'required|string|max:255',
            'memo'             => 'nullable|string',
        ]);

        $interaction = $company->interactions()->create([
            'interaction_date' => $validated['interaction_date'],
            'type'             => $validated['type'],
            'memo'             => $validated['memo'],
        ]);

        return redirect()->route('companies.show', $company->id);
    }

    // 編集・更新・削除も今後追加予定
}
