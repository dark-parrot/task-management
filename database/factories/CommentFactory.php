<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Comment::class;
    public function definition(): array
    {
        return [
            'task_id' => \App\Models\Task::inRandomOrder()->first()?->id ?? 1,
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'content' => $this->faker->paragraph,
        ];

    }
}
