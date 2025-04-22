<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->randomElement([
                'Customer Support', 'Sales', 'Technical Support', 'Billing',
                'Marketing', 'HR', 'IT', 'Operations', 'Complaints', 'Dispatch'
            ]),
            'description' => $this->faker->optional()->sentence,
            'active' => $this->faker->boolean(90),
            'organization_id' => Organization::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
