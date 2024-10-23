<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class);
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        $response = [];
        foreach ($categories as $category) {
            $response[] = [
                'user_id' => $category->user_id,
                'email' => $category->user->email,
                'name' => $category->name,
                'type' => $category->type ?? '',
            ];
        }
        return response()->json($response);
    }

    public function show(Category $category)
    {
        $response = [];
        foreach ($category->tasks as $task) {
            $response[] = [
                'task_id' => $task->id,
                'user_id' => $task->user_id,
                'email' => $task->user->email,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->getStatus(),
                'created_at' => $task->created_at,
                'finished_at' => $task->finished_at
            ];
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        try {
            Category::create($request->all());
        } catch (Exception $e) {
            response()->json(['Category not created. Has error: ' . $e->getMessage()], 400);
        }

        return response()->json(['Category created.']);
    }

    /**
     * @param Category $category
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Category $category, Request $request): JsonResponse
    {
        try {
            $category->update($request->all());
        } catch (Exception $e) {
            response()->json(['Category cannot be updated. Request return error: ' . $e->getMessage()], 400);
        }

        return response()->json(['Category has been updated']);
    }

    /**
     * @param Category $category
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(Category $category): JsonResponse
    {
        try {
            $category->delete();
        } catch (Exception $e) {
            response()->json(['Category cannot be deleted. Request return error: ' . $e->getMessage()], 400);
        }
        return response()->json(['Category has been deleted']);
    }
}
