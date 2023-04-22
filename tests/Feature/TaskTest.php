<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskTest extends TestCase
{
    use WithFaker;
    
    public function testUserCanChangeStatusOfAssignedTask()
    {
        // Create a User
        $user = User::factory()->create();

        // Create a Task assigned to the User
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        // Login the User
        $this->actingAs($user);

        // Change the status of the Task
        $response = $this->put("api/v1/team-member/task/{$task->id}", [
            'status' => 'COMPLETED', // Set the new status
        ]);
         
        // Assert that the Task was updated successfully
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'COMPLETED', // Assert that the status was changed
        ]);
    }

}
