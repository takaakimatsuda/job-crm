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

    // 編集内容の保存
    public function update(Request $request, Interaction $interaction)
    {
        $request->validate([
            'interaction_date' => 'required|date',
            'type' => 'required|string',
            'memo' => 'nullable|string',
        ]);

        $interaction->update($request->only('interaction_date', 'type', 'memo'));

        return back(); // Vue側が自動更新してくれるためリダイレクトでOK
    }

    // 削除処理
    public function destroy(Interaction $interaction)
    {
        $interaction->delete();

        return back(); // 同様に画面側でリストが更新される
    }
}
