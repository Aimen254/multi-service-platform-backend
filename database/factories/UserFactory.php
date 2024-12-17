<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('12345678')
        ];
    }

    /**
     * Attaching role based values.
     *
     * @return $this
     */
    public function attachDetails($type)
    {
        session()->put('type', $type);
        return $this->state(function (array $attributes) use ($type) {
            return [
                'user_type' => $type,
                'email_verified_at' => now(),
            ];
        });
    }
}
