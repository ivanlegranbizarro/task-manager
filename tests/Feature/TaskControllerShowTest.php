<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerShowTest extends TestCase
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
            ['creator_id' => $this->user->id]
        );
        $this->route = route('tasks.show', $this->task->id);
    }

    public function test_can_see_created_tasks(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->route);

        $response->assertOk()
            ->assertJsonFragment(['title' => $this->task->title])
            ->assertJsonFragment(['completed' => (bool) $this->task->completed]);
    }
}
