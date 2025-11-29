<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => \App\Models\Group::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+30 days'),
            'meeting_type' => fake()->randomElement(['virtual', 'in_person']),
            'meeting_link' => fake()->url(),
            'location' => fake()->address(),
        ];
    }
}
