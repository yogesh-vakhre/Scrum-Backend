<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiHelpers;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    
    use ApiHelpers; // Using the apiHelpers Trait

    /**
     * Get all projects with their associated tasks and users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $projects = Project::with('tasks.user')->get();
        return $this->onSuccess($projects, 'Project All Retrieved');
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
            'name' => 'required|string|max:255|unique:projects',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
           $errors = $validator->errors();
           return $this->onError(404,'Validation Error.',$errors);
        }
        
        $project = Project::create([
            'name' => $request->name,
        ]);
              
        return $this->onSuccess($project, 'Project Created',201);             
    }

    /**
     * Get a specific project with its associated tasks and users.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project): JsonResponse
    {
        // Load the associated tasks and users relationships
        $project->load('tasks.user');
        return $this->onSuccess($project, 'Project Retrieved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project): JsonResponse
    {
         // Load the associated tasks and users relationships
         $project->load('tasks.user');

        return $this->onSuccess($project, 'Project Retrieved');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $data = $request->all();   

        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:projects,name,'.$project->id
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
           $errors = $validator->errors();
           return $this->onError(404,'Validation Error.',$errors);
        }
        
        // Update project                
        $project->update($data);
              
        return $this->onSuccess($project, 'Project Updated');             
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project): JsonResponse
    {
        // Delete associated tasks
        $project->tasks()->delete();
       
        // Delete the specific project data
        $project->delete();
        return $this->onSuccess($project, 'Project Deleted');        
    }
}
