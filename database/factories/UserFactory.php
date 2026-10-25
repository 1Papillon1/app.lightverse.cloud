<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;

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
        return [
            'username' => fake()->unique()->userName(),
            'password' => 'password',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->address(),
            'storage_limit' => 3 * 1024 * 1024,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withRoles(string ...$roles): static
    {
        return $this->afterCreating(function ($user) use ($roles) {
            $user->roles()->attach(Role::whereIn('name', $roles)->pluck('id'));
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
   /*  public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    } */
}
