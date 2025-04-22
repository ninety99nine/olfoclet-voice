<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Queue;
use Illuminate\Database\Eloquent\Factories\Factory;

class QueueFactory extends Factory
{
    protected $model = Queue::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->randomElement([
                'General Support', 'Billing Queue', 'Technical Queue', 'Sales Queue', 'Priority Queue'
            ]),
            'description' => $this->faker->optional()->sentence,
            'active' => $this->faker->boolean(90),
            'organization_id' => Organization::factory(),
            'department_id' => $this->faker->optional()->randomElement(Department::pluck('id')->toArray()),
            'sla_threshold' => $this->faker->numberBetween(30, 120),
            'max_wait_time' => $this->faker->numberBetween(300, 1800),
            'escalation_threshold' => $this->faker->numberBetween(600, 3600),
            'avg_wait_time' => $this->faker->optional()->numberBetween(10, 300),
            'service_level' => $this->faker->optional()->randomFloat(2, 80, 100),
            'abandonment_rate' => $this->faker->optional()->randomFloat(2, 0, 20),
            'last_sla_review' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'call_volume_warning_threshold' => $this->faker->optional()->numberBetween(10, 50),
            'call_volume_critical_threshold' => $this->faker->optional()->numberBetween(50, 100),
            'record_calls' => $this->faker->boolean,
            'strategy' => $this->faker->randomElement(['round robin', 'ring all', 'least calls', 'longest idle', 'random']),
            'priority_level' => $this->faker->randomElement(['normal', 'vip']),
            'metadata' => $this->faker->optional()->passthrough(['key' => $this->faker->word]),
            'hold_music_url' => $this->faker->optional()->url,
            'greeting_message' => $this->faker->optional()->sentence,
            'wait_message' => $this->faker->optional()->sentence,
            'callback_enabled' => $this->faker->boolean,
            'fallback_queue_id' => $this->faker->optional()->randomElement(Queue::pluck('id')->toArray()),
            'fallback_department_id' => $this->faker->optional()->randomElement(Department::pluck('id')->toArray()),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
