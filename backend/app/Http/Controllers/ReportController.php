<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\ProductionUnit;
use App\Models\Herd;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Converte string para float, tratando diferentes formatos
     */
    private function parseNumericValue($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        $cleaned = preg_replace('/[^0-9.,]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    public function dashboard(): JsonResponse
    {
        try {
            $quantityProducers = Farmer::count();
            $quantityProperties = Property::count();
            $quantityProductionUnits = ProductionUnit::count();
            $quantityHerds = Herd::count();

            $propertiesByMunicipality = Property::select('municipality', DB::raw('count(*) as count'))
                ->groupBy('municipality')
                ->orderBy('count', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'municipality' => $item->municipality,
                        'count' => $item->count
                    ];
                });

            $animalsBySpecies = Herd::select('species', DB::raw('sum(quantity) as count'))
                ->whereIn('species', ['Bovino', 'Caprino', 'Suíno'])
                ->groupBy('species')
                ->orderBy('count', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'species' => $item->species,
                        'count' => (int) $item->count
                    ];
                });

            $hectaresByCrop = ProductionUnit::whereIn('crop_name', ['Laranja Pera', 'Melancia Crimson Sweet', 'Goiaba Paluma'])
                ->get()
                ->groupBy('crop_name')
                ->map(function($group, $cropName) {
                    $totalHectares = $group->sum(function($item) {
                        return $this->parseNumericValue($item->total_area_ha);
                    });
                    return [
                        'crop' => $cropName,
                        'hectares' => $totalHectares
                    ];
                })
                ->sortByDesc('hectares')
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'quantityProducers' => $quantityProducers,
                    'quantityProperties' => $quantityProperties,
                    'quantityProductionUnits' => $quantityProductionUnits,
                    'quantityHerds' => $quantityHerds,

                    'propertiesByMunicipality' => $propertiesByMunicipality,
                    'animalsBySpecies' => $animalsBySpecies,
                    'hectaresByCrop' => $hectaresByCrop,

                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar dados do dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
