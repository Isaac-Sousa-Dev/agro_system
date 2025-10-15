<?php

namespace App\Services;

use App\Services\AbstractService;
use App\Models\Farmer;

class FarmerService extends AbstractService
{
    public function __construct(Farmer $model)
    {
        parent::__construct($model);
    }

    public function create(array $data, array $properties = [])
    {
        $farmer = $this->model->create($data);
        if (!empty($properties)) {
        	$farmer->properties()->createMany($properties);
        }
        return $farmer;
    }

    public function update(array $data, $id, array $properties = [])
    {
        $farmer = $this->model->find($id);
        $farmer->update($data);

        $farmer->properties()->delete();
        if (!empty($properties)) {
            $farmer->properties()->createMany($properties);
        }
        return $farmer;
    }
}
