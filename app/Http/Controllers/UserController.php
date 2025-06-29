<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(IndexUserRequest $request): JsonResponse
    {
        $result = $this->userService->index(
            $request->input('per_page', 15),
            $request->input('page', 1),
            $request->input('order_by', 'created_at'),
            $request->input('order_direction', 'desc'),
            $request->getSelectColumns(),
            $request->input('with', []),
            $request->input('filters', []),
            $request->input('search')
        );

        $statusCode = $result['success'] ? 200 : 500;
        return response()->json($result, $statusCode);
    }

    public function show(ShowUserRequest $request, string $id): JsonResponse
    {
        $result = $this->userService->show($id, $request->input('with', []));

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $result = $this->userService->store($request->validated());

        $statusCode = $result['success'] ? 201 : 500;
        return response()->json($result, $statusCode);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $result = $this->userService->update($id, $request->validated());

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    public function destroy(string $id): JsonResponse
    {
        $result = $this->userService->destroy($id);

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }
}
