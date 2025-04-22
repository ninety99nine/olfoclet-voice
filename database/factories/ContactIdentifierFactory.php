<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactIdentifier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactIdentifierFactory extends Factory
{
    protected $model = ContactIdentifier::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'contact_id' => Contact::factory(),
            'type' => $this->faker->randomElement(['phone', 'email', 'external_id']),
            'value' => function (array $attributes) {
                if ($attributes['type'] === 'phone') {
                    return $this->faker->unique()->e164PhoneNumber;
                } elseif ($attributes['type'] === 'email') {
                    return $this->faker->unique()->safeEmail;
                } else {
                    return $this->faker->uuid;
                }
            },
            'is_primary' => $this->faker->boolean(50),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
