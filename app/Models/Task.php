<?php

namespace App\Models;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'completed', 'project_id'];

    protected $hidden = ['updated_at'];


    public function scopeCompleted($query, $completed)
    {
        if (! is_null($completed)) {
            return $query->where('completed', $completed);
        }
        return $query;
    }

    public function scopeTitle($query, $title)
    {
        if (! is_null($title)) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }

    public function scopeOrderByTitle($query, $order = 'asc')
    {
        if (! is_null($order)) {
            if ($order === 'asc') {
                return $query->orderBy('title');
            } elseif ($order === 'desc') {
                return $query->orderByDesc('title');
            }
        }
        return $query;
    }

    protected static function booted(): void
    {
        if (!app()->runningInConsole()) {
            static::creating(function ($task) {
                $task->creator_id = auth()->id();
            });
        }
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
