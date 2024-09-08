<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'completed'];

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
            return $query;
        }
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
