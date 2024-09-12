<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerDeleteTest extends TestCase
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
        $this->route = route('tasks.destroy', $this->task->id);
    }

    public function test_can_delete_task(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this->deleteJson($this->route);
        $response->assertNoContent();
    }
}
