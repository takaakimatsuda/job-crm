<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->get();

        return Inertia::render('Company/Index', [
            'companies' => $companies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Company/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'status'         => 'nullable|string|max:50',
            'hope_level'     => 'nullable|integer|min:1|max:5',
            'tags'           => 'nullable|array',
            'tags.*'         => 'string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'website_url'    => 'nullable|url|max:255',
            'memo'           => 'nullable|string',
        ]);

        // Company作成（tags除外）
        $company = Company::create(Arr::except($validated, ['tags']));

        // タグ登録・紐づけ
        if (!empty($validated['tags'])) {
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $company->tags()->attach($tag->id);
            }
        }

        return redirect()->route('companies.index')->with('success', '企業を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        // 必要に応じて関連情報も付ける
        $company->load('interactions'); // ←後で履歴も使う前提でリレーションを準備

        return Inertia::render('Company/Show', [
            'company' => $company,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}
