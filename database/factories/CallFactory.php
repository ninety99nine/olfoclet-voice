<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\Contact;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallFactory extends Factory
{
    protected $model = Call::class;

    public function definition(): array
    {
        $started_at = $this->faker->dateTimeBetween('-6 months', 'now');
        $answered_at = $this->faker->optional(0.8)->dateTimeBetween($started_at, (clone $started_at)->modify('+1 minute'));
        $ended_at = $answered_at ? $this->faker->dateTimeBetween($answered_at, (clone $answered_at)->modify('+10 minutes')) : null;
        $duration = ($ended_at && $started_at) ? (int) $ended_at->getTimestamp() - $started_at->getTimestamp() : null;

        return [
            'id' => $this->faker->uuid,
            'direction' => $this->faker->randomElement(['inbound', 'outbound']),
            'status' => $this->faker->randomElement(['initiated', 'ringing', 'in-progress', 'completed', 'failed']),
            'from' => $this->faker->e164PhoneNumber,
            'to' => $this->faker->e164PhoneNumber,
            'started_at' => $started_at,
            'answered_at' => $answered_at,
            'ended_at' => $ended_at,
            'hold_time' => $this->faker->optional()->numberBetween(0, 300),
            'duration' => $duration,
            'disposition' => $this->faker->optional()->randomElement(['resolved', 'escalated', 'callback', 'follow-up']),
            'transfer_count' => $this->faker->numberBetween(0, 3),
            'recording_url' => $this->faker->optional()->url,
            'session_id' => $this->faker->optional()->uuid,
            'metadata' => $this->faker->optional()->passthrough(['key' => $this->faker->word]),
            'notes' => $this->faker->optional()->paragraph,
            'ai_summary' => $this->faker->optional()->paragraph,
            'ai_suggested_actions' => $this->faker->optional()->passthrough(['action' => $this->faker->word]),
            'organization_id' => Organization::factory(),
            'queue_id' => $this->faker->optional()->randomElement(Queue::pluck('id')->toArray()),
            'department_id' => $this->faker->optional()->randomElement(Department::pluck('id')->toArray()),
            'agent_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
            'contact_id' => $this->faker->optional()->randomElement(Contact::pluck('id')->toArray()),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
