<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use App\Services\PropertyService;

class PropertyController extends Controller
{

    private $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Property::with(['farmer', 'productionUnits', 'herds']);

        // Search by property name or farmer name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhereHas('farmer', function($farmerQuery) use ($search) {
                      $farmerQuery->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        // Filter by municipality
        if ($request->has('municipality')) {
            $query->where('municipality', $request->get('municipality'));
        }

        // Filter by state
        if ($request->has('state')) {
            $query->where('state', $request->get('state'));
        }

        // Filter by farmer
        if ($request->has('farmer_id')) {
            $query->where('farmer_id', $request->get('farmer_id'));
        }

        $properties = $query->orderBy('id', 'desc')->paginate(6);

        return PropertyResource::collection($properties)
        ->response()
        ->setStatusCode(200);
    }


    public function store(PropertyRequest $request): JsonResponse
    {
        try {
            $property = $this->propertyService->create($request->all(), $request->productionUnits, $request->herds);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Property not created',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Property created successfully',
            'data' => $property
        ], 201);
    }


    public function show(Property $property): JsonResponse
    {
        return response()->json([
            'data' => $property->load(['farmer', 'productionUnits', 'herds'])
        ]);
    }


    public function update(PropertyRequest $request, Property $property): JsonResponse
    {
        try {
            $updatedProperty = $this->propertyService->update($request->all(), $property->id, $request->productionUnits, $request->herds);

            return (new PropertyResource($updatedProperty))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Property not updated',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Property $property): JsonResponse
    {
        try {
            $property = $this->propertyService->delete($property->id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Property not deleted',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Property deleted successfully'
        ]);

    }


    public function export(Request $request): JsonResponse
    {
        // This will be implemented later with Excel export
        return response()->json([
            'message' => 'Export functionality will be implemented'
        ]);
    }
}
