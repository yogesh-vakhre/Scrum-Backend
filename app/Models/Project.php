<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UUID;
use App\Models\User;
use App\Models\Task;

class Project extends Model
{
    use HasFactory, UUID;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];
    
    /**
     * Define the relationship with the Task model
     *
     * @return \App\Models\Project  $project
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Define the relationship with the User model
     *
     * @return \App\Models\User  $user
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
