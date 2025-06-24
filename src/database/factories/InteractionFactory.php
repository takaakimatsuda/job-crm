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
        return [
            'interaction_date' => $this->faker->date(),
            'type'             => $this->faker->randomElement(['電話', '面談', 'メール']),
            'memo'             => $this->faker->paragraph(),
            'summary'          => $this->faker->sentence(),
        ];
    }
}
