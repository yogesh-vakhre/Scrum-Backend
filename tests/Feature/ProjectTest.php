<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class ProjectTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test Create Project With Users.
     *
     * @return void
     */
    public function testCreateProjectWithUsers()
    {
        // Create two product owner
       $user = User::factory()->create(["role"=>2]);
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $usersData=[$user1->id, $user2->id];

        $projectData = [
            'name' => $this->faker->unique()->word(),
        ];

        // Login the User
        $this->actingAs($user);

        // Create a project and assign users to it
        $project = Project::create($projectData);
        $project->users()->attach($usersData);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => $projectData['name'],
        ]); // Assert that the project is created in the database

        // Assert that the users have been assigned to the project
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $user1->id,
        ]);

    }
}



