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
    public function test_filter_tasks_by_completed_status(): void
    {
        Sanctum::actingAs($this->user);

        // Crear tareas con diferentes estados de completado
        Task::factory()->create(['completed' => false]);
        Task::factory()->create(['completed' => true]);

        // Filtrar por tareas no completadas (completado = 'No')
        $response = $this->getJson($this->route . '?completed=false');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['completed' => 'No']);  // Comprobar que el valor transformado es 'No'

        // Filtrar por tareas completadas (completado = 'Yes')
        $response = $this->getJson($this->route . '?completed=true');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['completed' => 'Yes']);  // Comprobar que el valor transformado es 'Yes'
    }



    public function test_filter_tasks_by_title(): void
    {
        Sanctum::actingAs($this->user);

        // Crear tareas con diferentes títulos
        Task::factory()->create(['title' => 'Buy groceries']);
        Task::factory()->create(['title' => 'Clean house']);

        // Filtrar por título que contiene "Buy"
        $response = $this->getJson($this->route . '?title=Buy');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Buy groceries']);

        // Filtrar por título que contiene "Clean"
        $response = $this->getJson($this->route . '?title=Clean');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Clean house']);
    }

    public function test_order_tasks_by_title(): void
    {
        Sanctum::actingAs($this->user);

        // Crear tareas con diferentes títulos
        Task::factory()->create(['title' => 'Apple']);
        Task::factory()->create(['title' => 'Banana']);
        Task::factory()->create(['title' => 'Carrot']);

        // Ordenar de manera ascendente
        $response = $this->getJson($this->route . '?order=asc');
        $response->assertOk()
            ->assertJsonPath('data.0.title', 'Apple')
            ->assertJsonPath('data.1.title', 'Banana')
            ->assertJsonPath('data.2.title', 'Carrot');

        // Ordenar de manera descendente
        $response = $this->getJson($this->route . '?order=desc');
        $response->assertOk()
            ->assertJsonPath('data.0.title', 'Carrot')
            ->assertJsonPath('data.1.title', 'Banana')
            ->assertJsonPath('data.2.title', 'Apple');
    }
}
