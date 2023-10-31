<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo' => 'storage/images/'.($this->faker->randomDigit()+1).'.jpg',
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
