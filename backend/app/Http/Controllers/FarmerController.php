<?php

namespace App\Http\Controllers;

use App\Http\Requests\FarmerRequest;
use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Services\FarmerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class FarmerController extends Controller
{

    private $farmerService;

    public function __construct(FarmerService $farmerService)
    {
        $this->farmerService = $farmerService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Farmer::with('properties');

        // if ($request->has('search')) {
        //     $search = $request->get('search');
        //     $query->where(function($query) use ($search) {
        //         $query->where('name', 'ilike', "%{$search}%")
        //           ->orWhere('cpf_cnpj', 'ilike', "%{$search}%");
        //     });
        // }

        $farmers = $query->orderBy('id', 'desc')->paginate(6);
        return response()->json($farmers);
    }


    public function store(FarmerRequest $request): JsonResponse
    {
        try {
            $properties = $request->properties;
            $farmer = $this->farmerService->create($request->all(), $properties);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Farmer not created',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Farmer created successfully',
            'data' => $farmer->load('properties')
        ], 201);
    }


    public function show(Farmer $farmer): JsonResponse
    {
        return response()->json([
            'data' => $farmer->load('properties.productionUnits', 'properties.herds')
        ]);
    }


    public function update(FormRequest $request, Farmer $farmer): JsonResponse
    {
        try {
            $properties = $request->properties;
            $farmer = $this->farmerService->update($request->all(), $farmer->id, $properties);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Farmer not updated',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Farmer updated successfully',
            'data' => $farmer->load('properties')
        ], 200);
    }


    public function destroy(Farmer $farmer): JsonResponse
    {
        try {
            $farmer = $this->farmerService->delete($farmer->id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Farmer not deleted',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Farmer deleted successfully'
        ]);
    }
}
