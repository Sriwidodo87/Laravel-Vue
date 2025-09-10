<?php

namespace Tests\Feature\API\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
use RefreshDatabase;

    public function test_user_can_get_list_of_tasks():void{

        //Arrange : create 2 fake tasks
        $tasks = Task::factory()->count(2)->create();

        //Act: Make a get request to the end point
        $response = $this->getJson('/api/v1/tasks');


        //Assert
        $response->assertOk();
        $response->assertJsonCount(2,'data');
        $response->assertJsonStructure([
            'data'=>[
                ['id','name','is_completed']
            ]
            ]);

    }

    public function test_user_can_get_single_task() : void {

        //arrange : create a task
        $task = Task::factory()->create();

        //action : Get Request end Point with task ID
        $response = $this->getJson('/api/v1/tasks/'.$task->id);


        // Assert
        $response->assertJson([
            'data'=>[
                'id' =>$task->id,
                'name' =>$task->name,
                'is_completed' =>$task->is_completed,
            ]
            ]);

    }

    public function test_user_can_create_a_task(): void
    {
         $response = $this->postJson('/api/v1/tasks',[
            'name'=>'New task'
         ]);
        $response->assertCreated();
        $response->assertJsonStructure([
            'data' =>['id','name','is_completed']
        ]);

        $this->assertDatabaseHas('tasks',[
            'name'=>'New task'
        ]);
    }

    public function test_user_cannot_create_invalid_task():  void
    {
        $response = $this->postJson('/api/v1/tasks',[
            'name'=>''
         ]);

         $response->assertStatus(422);
         $response->assertJsonValidationErrorFor('name');
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson('/api/v1/tasks/'.$task->id,[
            'name'=>'Updated Task'
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'name'=>'Updated Task'
        ]);

    }
    public function test_user_cannot_update_task_with_invalid_data(): void
    {
        $task = Task::factory()->create();
         $response = $this->putJson('/api/v1/tasks/'.$task->id,[
            'name'=>''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_toggle_task_completion(): void
     {
       $task = Task::factory()->create([
        'is_completed'=>false
       ]) ;

       $response = $this->patchJson('/api/v1/tasks/'.$task->id.'/complete',[
        'is_completed'=> true
       ]);
       $response->assertOk();
       $response->assertJsonFragment([
        'is_completed'=> true
       ]);
    }
    public function test_user_cannot_toggle_task_completed_with_invalid_data() : void
     {
       $task =Task::factory()->create() ;

       $response = $this->patchJson('/api/v1/tasks/'.$task->id.'/complete',[
        'is_completed'=> 'yes'
       ]);
       $response->assertStatus(422);
       $response->assertJsonValidationErrors(['is_completed']);
    }


    public function test_user_can_delete_task() : void {
        $task = Task::factory()->create();
        $response= $this->deleteJson('/api/v1/tasks/'.$task->id);
        $response ->assertNoContent();
        $this->assertDatabaseMissing('tasks',[
            'id'=> $task -> id,
        ]);
    }


}
