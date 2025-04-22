<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\CustomAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomAttributeFactory extends Factory
{
    protected $model = CustomAttribute::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'organization_id' => Organization::factory(),
            'name' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement(['string', 'url', 'number', 'date']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
