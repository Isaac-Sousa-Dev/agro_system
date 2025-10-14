<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use Illuminate\Http\JsonResponse;

class FarmerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Farmer::with('properties');

        // Search by name or CPF/CNPJ
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('cpf_cnpj', 'ilike', "%{$search}%");
            });
        }

        // Filter by municipality
        if ($request->has('municipality')) {
            $query->whereHas('properties', function($q) use ($request) {
                $q->where('municipality', $request->get('municipality'));
            });
        }

        $farmers = $query->paginate(15);

        return response()->json($farmers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf_cnpj' => 'required|string|unique:farmers,cpf_cnpj',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'registration_date' => 'required|date',
        ]);

        $farmer = Farmer::create($request->all());

        return response()->json([
            'message' => 'Farmer created successfully',
            'data' => $farmer->load('properties')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Farmer $farmer): JsonResponse
    {
        return response()->json([
            'data' => $farmer->load('properties.productionUnits', 'properties.herds')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farmer $farmer): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf_cnpj' => 'required|string|unique:farmers,cpf_cnpj,' . $farmer->id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'registration_date' => 'required|date',
        ]);

        $farmer->update($request->all());

        return response()->json([
            'message' => 'Farmer updated successfully',
            'data' => $farmer->load('properties')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmer $farmer): JsonResponse
    {
        $farmer->delete();

        return response()->json([
            'message' => 'Farmer deleted successfully'
        ]);
    }
}
