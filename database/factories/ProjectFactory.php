<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $uniqueName = $this->uniqueName();
        return [
            'name' => $uniqueName,
        ];
    }

    /**
     * Generate a unique project name.
     *
     * @return string
     */
    protected function uniqueName()
    {
        // Generate a unique name
        $name = $this->faker->unique()->word;

        // Check if the generated name already exists in the database
        $count = Project::where('name', $name)->count();

        // If the generated name already exists, regenerate it
        if ($count > 0) {
            return $this->uniqueName();
        }

        return $name;
    }
}
