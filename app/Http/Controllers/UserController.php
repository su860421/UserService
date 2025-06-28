<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display all Users (supports pagination, sorting, relationships, filtering)
     */
    public function index(IndexUserRequest $request)
    {
        $perPage = $request->get('per_page', 0);
        $orderBy = $request->get('order_by');
        $orderDirection = $request->get('order_direction', 'asc');
        $relationships = $request->get('with', []);
        $columns = $request->get('columns', ['*']);
        $filters = $request->get('filters', []);

        $users = $this->userService->index(
            $perPage,
            $orderBy,
            $orderDirection,
            $relationships,
            $columns,
            $filters
        );

        return response()->json($users);
    }

    /**
     * Display specific User
     */
    public function show(ShowUserRequest $request, $id)
    {
        try {
            $columns = $request->get('columns', ['*']);
            $relationships = $request->get('with', []);
            $user = $this->userService->find(
                $id,
                $columns,
                $relationships
            );
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Create new User
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create(
            $request->validated()
        );
        return response()->json($user, 201);
    }

    /**
     * Update User
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->update(
            $id,
            $request->validated()
        );
        return response()->json($user);
    }

    /**
     * Delete User
     */
    public function destroy($id)
    {
        $this->userService->delete($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
