<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerIndexTest extends TestCase
{
    use RefreshDatabase;

    protected string $route;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->route = route('tasks.index');
    }

    public function test_only_authenticated_users_can_see_tasks(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }

    public function test_authenticated_users_can_see_tasks(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->route);

        $response->assertOk();
    }

    public function test_task_json_structure(): void
    {
        Sanctum::actingAs($this->user);

        Task::factory()->create();

        $response = $this->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'completed',
                    ],
                ],
            ]);
    }
}
