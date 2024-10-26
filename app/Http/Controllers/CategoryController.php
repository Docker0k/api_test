<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        /** @var Category[] $categories */
        if (Auth::user()->hasRole('admin')){
            $categories = Category::all();
        } else {
            $categories = Category::query()
                ->where('user_id', Auth::user()->id)
                ->get();
        }
        $response = [];
        foreach ($categories as $category) {
            $response[] = [
                'category_id' => $category->id,
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
            $arr = [
                'name' => $request->name,
                'type' => $request->type ?? '',
                'user_id' => Auth::id()
            ];
            Category::create($arr);

        } catch (Exception $e) {
            return response()->json(['Category not created. Has error: ' . $e->getMessage()], 400);
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
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'string|max:255',
        ]);

        try {
            $category->update($request->all());
        } catch (Exception $e) {
            return response()->json(['Category cannot be updated. Request return error: ' . $e->getMessage()], 400);
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
            return response()->json(['Category cannot be deleted. Request return error: ' . $e->getMessage()], 400);
        }
        return response()->json(['Category has been deleted']);
    }
}
