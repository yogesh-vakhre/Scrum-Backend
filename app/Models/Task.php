<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UUID;
use App\Models\User;
use App\Models\Project;

class Task extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'project_id',
        'user_id'
    ];
 
    /**
     * Define the relationship with the Project model
     *
     * @return \App\Models\Project  $project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Define the relationship with the User model
     *
     * @return \App\Models\User  $user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
