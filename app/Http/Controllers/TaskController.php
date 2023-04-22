<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiHelpers;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    use ApiHelpers; // Using the apiHelpers Trait

    /**
     * Get all tasks for a specific project.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $projectId= $request->projectId;
        $tasks = Task::with('project', 'user')->where('project_id', $projectId)->get();
        return $this->onSuccess($tasks, 'Task for a specific project all retrieved');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'project_id' => 'required',
            'user_id' => 'required',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
           $errors = $validator->errors();
           return $this->onError(404,'Validation Error.',$errors);
        }
        
        // Return errors if project not found error occur.
        $project = Project::find($request->project_id);
        if (empty($project)) {        
            return $this->onError(404,'Project not found');
        }
        // Return errors if user not found error occur.
        $user = User::find($request->user_id);
        if (empty($user )) {        
            return $this->onError(404,'User not found');
        }
        $task = Task::create($request->all());
              
        return $this->onSuccess($task, 'Task Created',201);
    }

    /**
     * Get a specific task with its associated project and user.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task): JsonResponse
    {
        // Load the associated tasks and users relationships
        $task->load('project','user');

        return $this->onSuccess($task, 'Task with its associated project and user retrieved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task): JsonResponse
    {
        // Load the associated tasks and users relationships
        $task->load('project','user');
        
        return $this->onSuccess($task, 'Task Retrieved');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $data = $request->all();   

        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'project_id' => 'required',
            'user_id' => 'required',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(404,'Validation Error.',$errors);
        }
        
        // Return errors if project not found error occur.
        $project = Project::find($request->project_id);
        if (empty($project)) {        
            return $this->onError(404,'Project not found');
        }
        // Return errors if user not found error occur.
        $user = User::find($request->user_id);
        if (empty($user )) {        
            return $this->onError(404,'User not found');
        }

        // Update project                
        $task->update($data);                
        return $this->onSuccess($task, 'Task Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete(); // Delete the specific task data
        return $this->onSuccess($task, 'Task Deleted');
    }
}
