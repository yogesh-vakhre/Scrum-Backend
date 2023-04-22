<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiHelpers;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiHelpers; // Using the apiHelpers Trait

    
    /**
     * Store a newly registered resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:10',
            'role' => 'required',

        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(404,'Validation Error.',$errors);
        }
        // Check if validation pass then create user and auth token. Return the auth token
        if ($validator->passes()) {
            // Check if role is Admin
            if($request->role ===1){
                return $this->onError(401, 'Unauthorized Access');
            }
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,

            ]);

            if($user->role===2){
                $role=['PRODUCT_OWNER'];
            }else{
                $role=['TEAM_MEMBER'];
            }
            $token = $user->createToken('auth_token',$role)->plainTextToken;
            $data['token'] =  $token;
            $data['user'] =  $user;     
            return $this->onSuccess($data, 'Team Memeber Created',201);
        }
    }

    /**
     * login a newly registered resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'password' => 'required',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(404,'Validation Error.',$errors);
        }
        // Return errors if details not valid.
        if (!Auth::attempt($request->only('username', 'password'))) {
            return $this->onError(403,'Invalid login details');
        }
        $user = User::where('username', $request->username)->firstOrFail();
        if($user->role===1){
            $role=['ADMIN'];
        }elseif($user->role===2){
            $role=['PRODUCT_OWNER'];
        }else{
            $role=['TEAM_MEMBER'];
        }
  
        $token = $user->createToken('auth_token',$role)->plainTextToken;
        $data['token'] =  $token;
        $data['user'] =  $user;

        return $this->onSuccess($data, 'User Logged In Successfully');
    }

    /**
     * Current User detials resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request): JsonResponse
    {
        return $this->onSuccess($request->user(), 'Current User Retrieved');
    }

    /**
     * Current User logout resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return $this->onSuccess($request->user(), 'User Logged Out Successfully');
    }

    /**
     * Team member can only change the status of the task assigned to them.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function teamMemberTaskUpdate(Request $request, $taskid): JsonResponse
    {
        $task=$request->user()->tasks()->findOrFail($taskid);
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(404,'Validation Error.',$errors);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ];   
        // Update project                
        $task->where('user_id',$request->user()->id)->update($data);                
        return $this->onSuccess($task, 'Team Member Task Updated');
    }

    /**
     * Get all Team member with their associated tasks and projects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function teamMemberProjects(Request $request): JsonResponse
    {        
       $userId= $request->user()->id;
       $projects = Project::with(['tasks.user'])->whereHas('tasks.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->get();     
        return $this->onSuccess($projects, 'Team Member Assigned Projects All Retrieved');
    }
}
