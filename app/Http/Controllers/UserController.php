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

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 500;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '取得使用者列表失敗', 500);
        }
    }

    public function show(ShowUserRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->userService->find($id, $request->input('with', []));

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 404;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '取得使用者資訊失敗', 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->create($request->validated());

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ], 201);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 500;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '建立使用者失敗', 500);
        }
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->userService->update($id, $request->validated());

            if ($result['success']) {
                return response()->json([
                    'result' => $result['data'],
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 404;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '更新使用者失敗', 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->userService->delete($id);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message']
                ]);
            }

            $code = $result['code'] ?? $result['status_code'] ?? 404;
            return response()->json([
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], $code);
        } catch (Exception $e) {
            return $this->handleException($e, '刪除使用者失敗', 500);
        }
    }
}
