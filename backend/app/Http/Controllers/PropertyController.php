<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use App\Services\PropertyService;
use App\Exports\PropertiesExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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


    public function store(PropertyRequest $request)
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


    public function export(Request $request)
    {
        try {
            // Coletar filtros da requisição
            $filters = $request->only(['search', 'municipality', 'state', 'farmer_id']);

            // Gerar nome do arquivo com timestamp
            $fileName = 'propriedades_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // Criar instância do export com filtros
            $export = new PropertiesExport($filters);

            // Retornar download do arquivo Excel
            return Excel::download($export, $fileName);

        } catch (\Exception $e) {
            // Em caso de erro, retornar resposta JSON com erro
            return response()->json([
                'message' => 'Erro ao exportar propriedades',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportPreview(Request $request): JsonResponse
    {
        try {
            // Coletar filtros da requisição
            $filters = $request->only(['search', 'municipality', 'state', 'farmer_id']);

            // Criar instância do export com filtros
            $export = new PropertiesExport($filters);

            // Obter dados para preview (limitado a 10 registros)
            $query = Property::with(['farmer', 'productionUnits', 'herds']);

            // Aplicar os mesmos filtros
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhereHas('farmer', function($farmerQuery) use ($search) {
                          $farmerQuery->where('name', 'ilike', "%{$search}%");
                      });
                });
            }

            if (isset($filters['municipality']) && !empty($filters['municipality'])) {
                $query->where('municipality', $filters['municipality']);
            }

            if (isset($filters['state']) && !empty($filters['state'])) {
                $query->where('state', $filters['state']);
            }

            if (isset($filters['farmer_id']) && !empty($filters['farmer_id'])) {
                $query->where('farmer_id', $filters['farmer_id']);
            }

            $totalRecords = $query->count();
            $previewData = $query->orderBy('id', 'desc')->limit(10)->get();

            // Mapear dados para preview
            $mappedData = $previewData->map(function($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->name,
                    'municipality' => $property->municipality,
                    'state' => $property->state,
                    'state_registration' => $property->state_registration,
                    'total_area' => number_format($property->total_area, 2, ',', '.'),
                    'farmer_name' => $property->farmer->name ?? 'N/A',
                    'farmer_cpf' => $property->farmer->cpf ?? 'N/A',
                    'production_units_count' => $property->productionUnits->count(),
                    'herds_count' => $property->herds->count(),
                    'created_at' => $property->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $property->updated_at->format('d/m/Y H:i:s'),
                ];
            });

            return response()->json([
                'message' => 'Preview da exportação gerado com sucesso',
                'data' => [
                    'total_records' => $totalRecords,
                    'preview_records' => $mappedData,
                    'filters_applied' => $filters
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar preview da exportação',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
