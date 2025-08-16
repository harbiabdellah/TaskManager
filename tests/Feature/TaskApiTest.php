<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_tasks()
    {
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_own_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/tasks');
        $response->assertOk()->assertJsonFragment(['title' => $task->title]);
    }

    public function test_authenticated_user_can_create_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $data = ['title' => 'Test Task', 'status' => 'pending'];
        $response = $this->postJson('/api/tasks', $data);
        $response->assertCreated()->assertJsonFragment(['title' => 'Test Task']);
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $user->id]);
    }

    public function test_authenticated_user_can_update_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);
        $response = $this->putJson("/api/tasks/{$task->id}", ['title' => 'Updated']);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated']);
    }

    public function test_authenticated_user_can_delete_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
