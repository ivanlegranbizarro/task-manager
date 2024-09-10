<?php

namespace App\Models;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'creator_id'];


    protected static function booted(): void
    {
        if (!app()->runningInConsole()) {
            static::creating(function ($task) {
                $task->creator_id = auth()->id();
            });
        }
    }


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, Member::class, 'project_id', 'user_id');
    }
}
