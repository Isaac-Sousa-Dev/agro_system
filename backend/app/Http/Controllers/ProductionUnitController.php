<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductionUnitResource;
use App\Models\ProductionUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductionUnitController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $productionUnits = ProductionUnit::with('property')->orderBy('id', 'desc')->paginate(6);
        return ProductionUnitResource::collection($productionUnits)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
