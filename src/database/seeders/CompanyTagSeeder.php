<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Tag;

class CompanyTagSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $companies = Company::all();
        $tags = Tag::all();

        if ($companies->isEmpty() || $tags->isEmpty()) {
            $this->command->warn('No companies or tags found. Skipping company_tag seeding.');
            return;
        }

        $rows = [];

        foreach ($companies as $company) {
            // 各企業に1〜3個のタグをランダムで付与
            $randomTags = $tags->random(rand(1, min(3, $tags->count())));

            foreach ($randomTags as $tag) {
                $rows[] = [
                    'company_id' => $company->id,
                    'tag_id'     => $tag->id,
                    'created_at' => $now,
                ];
            }
        }

        // 一括insert
        DB::table('company_tag')->insert($rows);

        $this->command->info(count($rows) . ' company_tag records inserted.');
    }
}
