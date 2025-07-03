<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(IndexUserRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->index(
                $request->input('per_page') ?? 0,
                $request->input('order_by', 'created_at'),
                $request->input('order_direction', 'asc'),
                $request->input('with', []),
                $request->getSelectColumns(),
                $request->input('filters', [])
            );
            return response()->json($result);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->handleException($e, __('get-users-failed'), 500);
        }
    }

    public function show(ShowUserRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->userService->find($id, $request->getSelectColumns(), $request->input('with', []));
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-user-info-failed'), 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->create($request->validated());
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('create-user-failed'), 500);
        }
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->userService->update($id, $request->validated());
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('update-user-failed'), 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->userService->delete($id);
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('delete-user-failed'), 500);
        }
    }
}
