<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'status' => 'active', // or use: $this->faker->randomElement(['active', 'inactive'])
            'hope_level' => $this->faker->numberBetween(1, 5),
            'contact_person' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'website_url' => $this->faker->url,
            'memo' => $this->faker->realText(100),
        ];
    }
}
