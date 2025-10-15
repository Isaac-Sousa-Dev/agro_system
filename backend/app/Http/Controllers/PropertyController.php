<?php

namespace App\Http\Controllers;

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

        $properties = $query->paginate(15);

        return response()->json($properties);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'state_registration' => 'nullable|string|max:255',
            'total_area' => 'required|numeric|min:0',
            'farmer_id' => 'required|exists:farmers,id',
        ]);

        $property = Property::create($request->all());

        return response()->json([
            'message' => 'Property created successfully',
            'data' => $property->load(['farmer', 'productionUnits', 'herds'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property): JsonResponse
    {
        return response()->json([
            'data' => $property->load(['farmer', 'productionUnits', 'herds'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'state_registration' => 'nullable|string|max:255',
            'total_area' => 'required|numeric|min:0',
            'farmer_id' => 'required|exists:farmers,id',
        ]);

        $property->update($request->all());

        return response()->json([
            'message' => 'Property updated successfully',
            'data' => $property->load(['farmer', 'productionUnits', 'herds'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property): JsonResponse
    {
        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully'
        ]);
    }

    /**
     * Export properties to Excel
     */
    public function export(Request $request): JsonResponse
    {
        // This will be implemented later with Excel export
        return response()->json([
            'message' => 'Export functionality will be implemented'
        ]);
    }
}
