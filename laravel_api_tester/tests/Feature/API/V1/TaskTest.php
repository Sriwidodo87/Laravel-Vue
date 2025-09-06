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
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
