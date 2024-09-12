<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    protected string $route;
    protected Task $task;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->route = route('tasks.store');
    }

    public function test_cannot_create_task_without_title(): void
    {
        Sanctum::actingAs($this->user);
        $data = [
            'completed' => true,
            'creator_id' => $this->user->id
        ];

        $response = $this->postJson($this->route, $data);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonValidationErrors(['title']);
    }

    public function test_cannot_create_task_without_creator_id(): void
    {
        Sanctum::actingAs($this->user);
        $data = [
            'title' => 'Test title',
            'completed' => true
        ];

        $response = $this->postJson($this->route, $data);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonValidationErrors(['creator_id']);
    }
}
