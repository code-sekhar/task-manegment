<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Exception;
class TaskController extends Controller
{
    public function store(Request $request){
        try{
           $userId = $request->header('id');
           $task = Task::create([
               'user_id' => $userId,
               'title'=>$request->input('title'),
               'description'=>$request->input('description'),
           ]);
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task,
                'success' => true,
            ], 201);
        }catch (Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ],500);
        }
    }
    //Get All Task
    public function getAllTasks(Request $request){
        $userId = $request->header('id');
        $all = Task::where('user_id',$userId)->get();
        return response()->json($all);
    }
    //Specific task
    public function getTaskById(Request $request,$id){
        try{
            $userId = $request->header('id');
            $findTask = Task::where('id',$id)->where('user_id',$userId)->get();
            if($findTask->isEmpty()){
                return response()->json([
                    'message' => 'Task not found',
                    'success' => false,
                ],404);

            }
            $task = Task::where('user_id',$userId)->where('id',$id)->firstOrFail();
            if($task){
                return response()->json([
                    'task' => $task,
                    'success' => true,
                    'message' => 'Task retrieved successfully'
                ],201);
            }else{
                return response()->json([
                    'message' => 'Task not found',
                    'success' => false,
                ],404);
            }
        }catch (Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ],500);
        }
    }
    public function updateTask(Request $request,$id){
        $userId = $request->header('id');
        $findTask = Task::where('id',$id)->where('user_id',$userId)->get();
        if($findTask->isEmpty()){
            return response()->json([
                'message' => 'Task not found',
                'success' => false,
            ],404);

        }
        $task = Task::where('user_id',$userId)->where('id',$id)->update([
            'title'=>$request->input('title'),
            'description'=>$request->input('description')
        ]);
        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task,
        ],201);
    }
    //Mark as Completed
    public function markTaskAsCompleted(Request $request,$id){
        try{
            $userId = $request->header('id');
            $findTask = Task::where('id',$id)->where('user_id',$userId)->get();
            if($findTask->isEmpty()){
                return response()->json([
                    'message' => 'Task not found',
                    'success' => false,
                ],404);

            }
            $result = Task::where('id',$id)->where('user_id',$userId)->update([
                'is_completed'=>1
                //  'is_completed'=>$request->input('is_completed')
            ]);
            return response()->json([
                'message' => 'Task Completed successfully',
                'task' => $result,
            ],201);
        }catch (Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ],500);
        }
    }
    //delete task
    public function deleteTask(Request $request,$id){
        try{
            $userId = $request->header('id');
            $checkTask = Task::where('id',$id)->where('user_id',$userId)->get();
            if($checkTask->isEmpty()){
                return response()->json([
                    'message' => 'Task not found',
                    'success' => false,
                ],404);
            }
            $delete_task = Task::where('id',$id)->where('user_id',$userId)->delete();
            return response()->json([
                'message' => 'Task deleted successfully',
                'success' => true,
                'data' => $delete_task,
            ],201);
        }catch (Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ],500);
        }
    }
}
