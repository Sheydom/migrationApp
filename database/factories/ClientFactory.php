<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),

            'last_name' => fake()->lastName(),

            'email' => fake()->unique()->safeEmail(),

            'phone' => fake()->phoneNumber(),

            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),

            'nationality' => fake()->country(),

            'current_visa' => fake()->randomElement([

                '482',

                '500',

                '600',

                '485',

                'Bridging Visa A',

            ]),

            'expire_date' => fake()->dateTimeBetween('today', '+3 years'),

            'status' => fake()->randomElement([

                'New',

                'Pending',

                'In Progress',

                'Granted',

                'Refused',

            ]),

            'notes' => fake()->sentence(),

            'folder_path' => null,
        ];
    }
}
