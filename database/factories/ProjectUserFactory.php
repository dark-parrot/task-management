<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProjectUser;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectUser>
 */
class ProjectUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProjectUser::class;
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::inRandomOrder()->first()?->id ?? 1,
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
        ];

    }
}
