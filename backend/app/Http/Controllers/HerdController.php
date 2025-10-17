<?php

namespace App\Http\Controllers;

use App\Http\Requests\HerdRequest;
use App\Http\Resources\HerdResource;
use App\Models\Herd;
use App\Services\HerdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HerdController extends Controller
{

    private $herdService;
    public function __construct(HerdService $herdService)
    {
        $this->herdService = $herdService;
    }

    public function index(): JsonResponse
    {
        $herds = Herd::with('property')->orderBy('id', 'desc')->paginate(6);
        return HerdResource::collection($herds)
            ->response()
            ->setStatusCode(200);
    }


    public function store(HerdRequest $request): JsonResponse
    {
        try {
            $herd = $this->herdService->create($request->all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Herd not created',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'message' => 'Herd created successfully',
            'data' => $herd
        ], 201);
    }


    public function update(HerdRequest $request, string $id): JsonResponse
    {
        try {
            $herd = $this->herdService->update($request->all(), $id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Herd not updated',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'message' => 'Herd updated successfully',
            'data' => $herd
        ], 200);
    }


    public function destroy(string $id): JsonResponse
    {
        try {
            $herd = $this->herdService->delete($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Herd not deleted',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'message' => 'Herd deleted successfully',
            'data' => $herd
        ], 200);
    }
}
