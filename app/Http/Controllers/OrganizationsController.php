<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\OrganizationsServiceInterface;
use App\Http\Requests\Organizations\StoreOrganizationsRequest;
use App\Http\Requests\Organizations\UpdateOrganizationsRequest;
use App\Http\Requests\Organizations\IndexOrganizationsRequest;
use App\Http\Requests\Organizations\ShowOrganizationsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class OrganizationsController extends Controller
{
    protected $organizationsService;

    public function __construct(OrganizationsServiceInterface $organizationsService)
    {
        $this->organizationsService = $organizationsService;
    }

    /**
     * Display all Organizations (supports pagination, sorting, relationships, filtering)
     */
    public function index(IndexOrganizationsRequest $request): JsonResponse
    {
        try {
            $result = $this->organizationsService->index(
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
            return $this->handleException($e, __('get-organizations-failed'), 500);
        }
    }

    /**
     * Display specific Organization
     */
    public function show(ShowOrganizationsRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->organizationsService->find($id, $request->get('columns', ['*']), $request->input('with', []));
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('get-organizations-info-failed'), 500);
        }
    }

    /**
     * Create new Organization
     */
    public function store(StoreOrganizationsRequest $request): JsonResponse
    {
        try {
            $result = $this->organizationsService->create($request->validated());
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('create-organizations-failed'), 500);
        }
    }

    /**
     * Update Organization
     */
    public function update(UpdateOrganizationsRequest $request, string $id): JsonResponse
    {
        try {
            $result = $this->organizationsService->update($id, $request->validated());
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('update-organizations-failed'), 500);
        }
    }

    /**
     * Delete Organization
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->organizationsService->delete($id);
            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, __('delete-organizations-failed'), 500);
        }
    }

    protected function handleException(\Exception $e, string $defaultMessage, int $defaultCode = 500): \Illuminate\Http\JsonResponse
    {
        return parent::handleException($e, $defaultMessage, $defaultCode);
    }
}
