<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'company' => fake()->company(),
            'location' => fake()->city(),
            'website' => fake()->url(),
            'email' => fake()->safeEmail(),
            'description' => fake()->paragraph(5),
            'tags' => 'a, b, c',
        ];
    }
}
