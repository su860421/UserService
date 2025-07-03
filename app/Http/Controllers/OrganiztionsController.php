<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\OrganiztionsServiceInterface;
use App\Http\Requests\Organiztions\StoreOrganiztionsRequest;
use App\Http\Requests\Organiztions\UpdateOrganiztionsRequest;
use App\Http\Requests\Organiztions\IndexOrganiztionsRequest;
use App\Http\Requests\Organiztions\ShowOrganiztionsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class OrganiztionsController extends Controller
{
    protected $organiztionsService;

    public function __construct(OrganiztionsServiceInterface $organiztionsService)
    {
        $this->organiztionsService = $organiztionsService;
    }

    /**
     * Display all Organiztionss (supports pagination, sorting, relationships, filtering)
     */
    public function index(IndexOrganiztionsRequest $request): JsonResponse
    {
        try {
            $result = $this->organiztionsService->index(
                $request->input('per_page') ?? 0,
                $request->input('order_by', 'created_at'),
                $request->input('order_direction', 'asc'),
                $request->input('with', []),
                $request->get('columns', ['*']),
                $request->input('filters', [])
            );
            return response()->json($result);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->handleException($e, __('get-organiztions-failed'), 500);
        }
    }

    /**
     * Display specific Organiztions
     */
    public function show(ShowOrganiztionsRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->organiztionsService->find($id, $request->get('columns', ['*']), $request->input('with', []));
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-organiztions-info-failed'), 500);
        }
    }

    /**
     * Create new Organiztions
     */
    public function store(StoreOrganiztionsRequest $request): JsonResponse
    {
        try {
            $result = $this->organiztionsService->create($request->validated());
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('create-organiztions-failed'), 500);
        }
    }

    /**
     * Update Organiztions
     */
    public function update(UpdateOrganiztionsRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->organiztionsService->update($id, $request->validated());
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('update-organiztions-failed'), 500);
        }
    }

    /**
     * Delete Organiztions
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->organiztionsService->delete($id);
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('delete-organiztions-failed'), 500);
        }
    }

    protected function handleException(\Exception $e, string $defaultMessage, int $defaultCode = 500): \Illuminate\Http\JsonResponse
    {
        return parent::handleException($e, $defaultMessage, $defaultCode);
    }
}
