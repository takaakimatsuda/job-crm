<?php

namespace Database\Factories;

use App\Models\Interaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Interaction>
 */
class InteractionFactory extends Factory
{
    protected $model = Interaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ja_JP');

        return [
            'interaction_date' => $faker->date(),
            'type'             => $faker->randomElement(['電話', '面談', 'メール']),
            'memo' => $faker->randomElement([
                '本日、電話にて一次面談の調整を行った。',
                '採用担当の○○氏と初回面談を実施。',
                '書類選考を通過し、次回面接日を調整中。',
                '内定の連絡あり。条件確認を進める予定。',
                '残念ながら辞退の意向を伝えた。',
            ]),
            'summary'          => $faker->realText(40),
        ];
    }
}
