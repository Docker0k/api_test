<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return response()->json(User::all()->toArray());
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
}
