<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthDate = fake()->dateTimeBetween('-50 years', '-18 years');
        $age = now()->diffInYears($birthDate);
        
        return [
            'contact_number' => fake()->numerify('###########'), // 11 digits
            'password' => static::$password ??= Hash::make('password'),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->address(),
            'date_of_birth' => $birthDate->format('Y-m-d'),
            'age' => $age,
            'sex' => fake()->randomElement(['female', 'male']),
            'user_type' => fake()->randomElement(['donor', 'requester']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
