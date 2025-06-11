<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Task::class;
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'project_id' => \App\Models\Project::inRandomOrder()->first()?->id ?? 0,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'tags' => implode(',', $this->faker->words(2)),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done', 'pending', 'rejected']),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'estimated_minutes' => $this->faker->numberBetween(20, 240),
            'repeat_cycle' => $this->faker->numberBetween(0, 5),
        ];

    }
}
