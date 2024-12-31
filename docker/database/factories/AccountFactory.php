<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'password' =>  Hash::make('password'),
            'access_token' => Str::random(10),
            'refresh_token' => Str::random(10),
        ];
    }
}
