<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionUnitRequest;
use App\Http\Resources\ProductionUnitResource;
use App\Models\ProductionUnit;
use App\Services\ProductionUnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductionUnitController extends Controller
{
    private $productionUnitService;
    public function __construct(ProductionUnitService $productionUnitService)
    {
        $this->productionUnitService = $productionUnitService;
    }

    public function index(Request $request): JsonResponse
    {
        $productionUnits = ProductionUnit::with('property')->orderBy('id', 'desc')->paginate(6);
        return ProductionUnitResource::collection($productionUnits)
            ->response()
            ->setStatusCode(200);
    }

    public function show(ProductionUnit $productionUnit): JsonResponse
    {
        return (new ProductionUnitResource($productionUnit))
            ->response()
            ->setStatusCode(200);
    }

    public function store(ProductionUnitRequest $request): JsonResponse
    {
        try {
            $productionUnit = $this->productionUnitService->create($request->all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Production unit not created',
                'error' => $e->getMessage()
            ], 500);
        }

        return (new ProductionUnitResource($productionUnit))
            ->response()
            ->setStatusCode(201);
    }


    public function destroy(ProductionUnit $productionUnit): JsonResponse
    {
        try {
            $productionUnit = $this->productionUnitService->delete($productionUnit->id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Production unit not deleted',
                'error' => $e->getMessage()
                ], 500);
        }

        return response()->json([
            'message' => 'Production unit deleted successfully'
        ]);
    }
}
