<?php

namespace App\Services;

use App\Services\AbstractService;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PropertyService extends AbstractService
{
    public function __construct(Property $model)
    {
        parent::__construct($model);
    }

    public function create(array $data, $productionUnits = [], $herds = [])
    {
        DB::beginTransaction();

        try {
            $property = $this->model->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($productionUnits) {
            try {
                $property->productionUnits()->createMany($productionUnits);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        if ($herds) {
            try {
                $property->herds()->createMany($herds);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        DB::commit();

        return $property;
    }


    public function update(array $data, $propertyId, $productionUnits = [], $herds = [])
    {
        $property = $this->model->find($propertyId);

        // Processar upload da imagem se existir
        if (isset($data['image']) && $data['image']) {
            // Verificar se é um arquivo real ou um array com objectURL
            if (is_array($data['image']) && isset($data['image']['objectURL'])) {
                // Se for um blob URL, não processar o upload por enquanto
                // O frontend deve enviar como arquivo real
                unset($data['image']);
            } elseif (is_object($data['image']) && method_exists($data['image'], 'store')) {
                // Se for um arquivo real, processar o upload
                // Deletar imagem anterior se existir
                if ($property->image && Storage::disk('public')->exists($property->image)) {
                    Storage::disk('public')->delete($property->image);
                }

                $imagePath = $data['image']->store('properties', 'public');
                $data['image'] = $imagePath;
            } else {
                // Se não for nem arquivo nem array válido, remover
                unset($data['image']);
            }
        }

        $property->update($data);

        $property->productionUnits()->delete();
        if (!empty($productionUnits)) {
            $property->productionUnits()->createMany($productionUnits);
        }

        $property->herds()->delete();
        if (!empty($herds)) {
            $property->herds()->createMany($herds);
        }

        return $property;
    }

}
