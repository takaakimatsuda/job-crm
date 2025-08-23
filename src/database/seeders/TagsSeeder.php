<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $tags = [
            // 技術
            'React',
            'Vue',
            'TypeScript',
            'PHP',
            'Laravel',
            'Node.js',
            'Python',
            'Go',
            'AWS',
            'GCP',
            'SRE',
            'データベース',
            'AI',
            // 働き方・企業属性
            'リモート',
            '出社',
            'ハイブリッド',
            'フレックス',
            'フルフレックス',
            '副業可',
            '自社開発',
            '受託',
            'スタートアップ',
            '上場企業',
            'メガベンチャー',
            // 採用条件など
            '未経験歓迎',
            '英語必須',
            '残業少なめ',
        ];

        $rows = array_map(fn($name) => [
            'name'       => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], $tags);

        DB::table('tags')->upsert($rows, ['name'], ['updated_at']);
    }
}
