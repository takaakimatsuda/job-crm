<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Interaction;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::all()->each(function ($company) {
            Interaction::factory()
                ->count(3)
                ->create([
                    'company_id' => $company->id,
                ]);
        });
    }
}
