<?php

namespace App\Services;

use App\Services\AbstractService;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

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
