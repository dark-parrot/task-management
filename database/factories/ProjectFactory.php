<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Project::class;
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'tags' => implode(',', $this->faker->words(3)),
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done', 'pending', 'rejected']),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'estimated_minutes' => $this->faker->numberBetween(30, 600),
            'repeat_cycle' => $this->faker->numberBetween(0, 10),
        ];

    }
}
