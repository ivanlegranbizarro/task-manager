<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'creator' => UserResource::make($this->creator),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'created_at' => $this->created_at,
        ];
    }
}
