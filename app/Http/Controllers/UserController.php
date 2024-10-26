<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->authorizeResource(User::class);
    }

    /**
     *  Display a listing of the resource
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index()
    {
        $users = User::all();
        $response = [];
        foreach ($users as $user) {
            $total = 0;
            $arr = [
                'email' => $user->email,
            ];
            foreach ($user->categories as $category) {
                if (!$category->tasks->count()) {
                    continue;
                }
                $total += $category->tasks->count();
            }
            $arr['total_tasks'] = $total;
            $response[] = $arr;
        }
        return response()->json($response);
    }

    public function show(User $user)
    {
        $total = 0;
        $arr = [
            'email' => $user->email,
        ];
        foreach ($user->categories as $category) {
            $arr['categories'][] = [
                'name' => $category->name,
                'count_tasks' => $category->tasks->count()
            ];
            $total += $category->tasks->count();
        }
        $arr['total_tasks'] = $total;
        $response[] = $arr;

        return response()->json($response);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $user = User::create($request->all());

        Category::create([
            'user_id' => $user->id,
            'name' => 'Urgent tasks',
            'can_modify' => false
        ]);

        return response()->json(['OK']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::find($id)->delete();
        return response()->json(['User has been deleted']);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'role' => 'string',
            'name' => 'string'
        ]);
        try {
            $user->update($request->all());
            if($request->has('role')){
                $user->roles()->detach();
                $user->assignRole($request->role);
            }
        }catch (Exception $e){
            return response()->json(['Error: ' . $e]);
        }

        return response()->json(['Success change user data']);
    }
}
