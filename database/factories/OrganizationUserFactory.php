<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\User;
use App\Models\CallActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationUserFactory extends Factory
{
    protected $model = CallActivity::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'call_id' => Call::factory(), // Assumes a Call exists or is created
            'activity_type' => $this->faker->randomElement([
                'call started', 'call answered', 'call ended', 'status changed',
                'transfer initiated', 'transfer accepted', 'transfer rejected', 'transfer completed',
                'outcome determined', 'hold started', 'hold ended', 'notes taken',
                'ai summary generated', 'queue entered', 'queue exited',
                'recording started', 'recording stopped', 'ivr interaction'
            ]),
            'description' => $this->faker->optional()->sentence,
            'performed_by' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()), // Nullable user_id
            'performed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'metadata' => $this->faker->optional()->passthrough(['key' => $this->faker->word]),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
