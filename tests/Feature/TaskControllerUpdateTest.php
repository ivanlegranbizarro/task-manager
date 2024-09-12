<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected string $route;
    protected Task $task;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create(
            [
                'creator_id' => $this->user->id
            ]
        );
        $this->route = route('tasks.update', $this->task->id);
    }

    public function test_can_update_task(): void
    {
        Sanctum::actingAs($this->user);
        $data = [
            'title' => 'Test title updated',
            'completed' => true
        ];
        $this->task = Task::factory()->create();
        $response = $this->putJson($this->route, $data);
        $response->assertOk()->assertJsonFragment(['title' => $data['title'], 'completed' => $data['completed']]);
        $this->assertDatabaseHas('tasks', $data);
    }
}
