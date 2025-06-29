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
                $request->input('per_page', 15),
                $request->input('order_by', 'created_at'),
                $request->input('order_direction', 'desc'),
                $request->input('with', []),
                $request->getSelectColumns(),
                $request->input('filters', [])
            );

            $statusCode = $result['success'] ? 200 : 500;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '取得使用者列表失敗'
            ], $statusCode);
        }
    }

    public function show(ShowUserRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->userService->find($id, $request->input('with', []));

            $statusCode = $result['success'] ? 200 : 404;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '取得使用者資訊失敗'
            ], $statusCode);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->create($request->validated());

            $statusCode = $result['success'] ? 201 : 500;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '建立使用者失敗'
            ], $statusCode);
        }
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->userService->update($id, $request->validated());

            $statusCode = $result['success'] ? 200 : 404;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '更新使用者失敗'
            ], $statusCode);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->userService->delete($id);

            $statusCode = $result['success'] ? 200 : 404;
            return response()->json($result, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => '刪除使用者失敗'
            ], $statusCode);
        }
    }
}
