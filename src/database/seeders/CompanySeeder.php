<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => '株式会社サンプル', 'status' => '未応募', 'hope_level' => 3, 'memo' => 'エンジニア枠あり'],
            ['name' => 'Tech Innovators', 'status' => '面接中', 'hope_level' => 5, 'memo' => 'AI系企業'],
            ['name' => 'Creative Minds Inc.', 'status' => '応募済', 'hope_level' => 4, 'memo' => 'Webデザイン中心'],
            ['name' => 'NextGen Solutions', 'status' => '内定', 'hope_level' => 5, 'memo' => '希望通り'],
            ['name' => '未来開発株式会社', 'status' => '辞退', 'hope_level' => 2, 'memo' => '勤務地が遠い'],
            ['name' => '株式会社ゆめみ', 'status' => '未応募', 'hope_level' => 4, 'memo' => 'Reactメイン'],
            ['name' => 'Softbridge', 'status' => '選考中', 'hope_level' => 3, 'memo' => 'クラウド系'],
            ['name' => 'Digital Frontier', 'status' => '面談予定', 'hope_level' => 5, 'memo' => '雰囲気が良い'],
            ['name' => 'CloudBase Inc.', 'status' => '書類選考落ち', 'hope_level' => 2, 'memo' => '給与水準が低め'],
            ['name' => 'グローバルシステム', 'status' => '応募済', 'hope_level' => 3, 'memo' => 'Laravel使用'],
            ['name' => 'トライデント株式会社', 'status' => '未応募', 'hope_level' => 4, 'memo' => 'インフラ志向'],
            ['name' => 'ZenTech Japan', 'status' => '面接中', 'hope_level' => 4, 'memo' => '海外展開あり'],
            ['name' => 'LogicTree', 'status' => '未応募', 'hope_level' => 3, 'memo' => '規模小さめ'],
            ['name' => 'キャリアネット', 'status' => '辞退', 'hope_level' => 1, 'memo' => '業務内容が合わない'],
            ['name' => '株式会社フューチャーリンク', 'status' => '内定', 'hope_level' => 5, 'memo' => '理想的な職場']
        ];

        foreach ($companies as $data) {
            Company::create($data);
        }
    }
}
