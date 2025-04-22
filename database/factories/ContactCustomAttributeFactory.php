<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\CustomAttribute;
use App\Models\ContactCustomAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactCustomAttributeFactory extends Factory
{
    protected $model = ContactCustomAttribute::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'contact_id' => Contact::factory(),
            'custom_attribute_id' => CustomAttribute::factory(),
            'name' => $this->faker->word, // Overridden by seeder
            'type' => $this->faker->randomElement(['string', 'url', 'number', 'date']), // Overridden by seeder
            'value' => function (array $attributes) {
                $type = $attributes['type'];
                return match ($type) {
                    'string' => $this->faker->word,
                    'url' => $this->faker->url,
                    'number' => (string) $this->faker->numberBetween(1, 1000), // Cast to string
                    'date' => $this->faker->date(),
                    default => $this->faker->word,
                };
            },
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
