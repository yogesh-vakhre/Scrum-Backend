<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $description = Str::limit($this->faker->paragraph, 255);
        return [
            'title' => $this->faker->sentence,
            'description' => $description,
            'status' => $this->faker->randomElement(['NOT_STARTED', 'IN_PROGRESS', 'READY_FOR_TEST','COMPLETED']),
            'user_id' => User::factory(), 
            'project_id' => Project::factory(),
        ];
    }
}
