<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var Task[] $tasks */
        $tasks = Task::query()
            ->where('user_id', Auth::id())
            ->orderBy('category_id')
            ->get();

        $response = [];
        foreach ($tasks as $task) {
            $response[$task->category_id][] = [
                'name' => $task->name,
                'description' => $task->description,
                'created_at' => $task->created_at,
                'status' => $task->getStatus(),
                'category' => $task->category->name
            ];
        }

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            Task::create($request->all());
        } catch (Exception $e) {
            response()->json(['Task not created. Has error: ', $e->getMessage()], 400);
        }

        return response()->json();
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $response = [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
            'email' => $task->user->email,
            'name' => $task->name,
            'description' => $task->description,
            'status' => $task->getStatus(),
            'created_at' => $task->created_at,
            'finished_at' => $task->finished_at
        ];

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        try {
            $task->update($request->all());
        } catch (Exception $e) {
            response()->json(['Task cannot be updated. Request return error: ' . $e->getMessage()], 400);
        }
        return response()->json(['Task successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
        } catch (Exception $e) {
            response()->json(['Task cannot be deleted. Request return error: ' . $e->getMessage()], 400);
        }
        return response()->json(['Task has been deleted']);
    }
}
