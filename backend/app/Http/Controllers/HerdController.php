<?php

namespace App\Http\Controllers;

use App\Http\Requests\HerdRequest;
use App\Http\Resources\HerdResource;
use App\Models\Herd;
use App\Services\HerdService;
use App\Exports\HerdsByFarmerPdfExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function exportPdf(Request $request)
    {
        try {
            $farmerId = $request->get('farmer_id');

            // Validar se o produtor existe (se fornecido)
            if ($farmerId && !\App\Models\Farmer::find($farmerId)) {
                return response()->json([
                    'message' => 'Produtor não encontrado',
                    'error' => 'Farmer not found'
                ], 404);
            }

            // Criar instância do export
            $export = new HerdsByFarmerPdfExport($farmerId);

            // Gerar PDF
            $dompdf = $export->generate();

            // Gerar nome do arquivo
            $fileName = $farmerId
                ? 'rebanhos_produtor_' . $farmerId . '_' . now()->format('Y-m-d_H-i-s') . '.pdf'
                : 'rebanhos_todos_produtores_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            // Retornar PDF como download
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar PDF de rebanhos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportPdfPreview(Request $request): JsonResponse
    {
        try {
            $farmerId = $request->get('farmer_id');

            // Validar se o produtor existe (se fornecido)
            if ($farmerId && !\App\Models\Farmer::find($farmerId)) {
                return response()->json([
                    'message' => 'Produtor não encontrado',
                    'error' => 'Farmer not found'
                ], 404);
            }

            // Obter dados para preview
            $query = Herd::with(['property.farmer']);

            if ($farmerId) {
                $query->whereHas('property', function($q) use ($farmerId) {
                    $q->where('farmer_id', $farmerId);
                });
            }

            $herds = $query->orderBy('property.farmer.name')
                          ->orderBy('property.name')
                          ->orderBy('species')
                          ->limit(10)
                          ->get();

            $totalHerds = $query->count();
            $totalAnimals = $query->sum('quantity');

            // Agrupar por produtor para preview
            $farmersData = $herds->groupBy('property.farmer.name')->map(function($herdsGroup, $farmerName) {
                return [
                    'farmer_name' => $farmerName,
                    'herds_count' => $herdsGroup->count(),
                    'total_animals' => $herdsGroup->sum('quantity'),
                    'properties' => $herdsGroup->groupBy('property.name')->map(function($propertyHerds, $propertyName) {
                        return [
                            'property_name' => $propertyName,
                            'herds' => $propertyHerds->map(function($herd) {
                                return [
                                    'species' => $herd->species,
                                    'quantity' => $herd->quantity,
                                    'purpose' => $herd->purpose,
                                    'municipality' => $herd->property->municipality,
                                    'state' => $herd->property->state,
                                ];
                            })
                        ];
                    })
                ];
            });

            return response()->json([
                'message' => 'Preview do PDF gerado com sucesso',
                'data' => [
                    'total_herds' => $totalHerds,
                    'total_animals' => $totalAnimals,
                    'farmers_count' => $farmersData->count(),
                    'preview_data' => $farmersData,
                    'farmer_id' => $farmerId
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar preview do PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
