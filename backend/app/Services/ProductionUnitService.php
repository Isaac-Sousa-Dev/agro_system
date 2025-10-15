<?php

namespace App\Services;

use App\Services\AbstractService;
use App\Models\ProductionUnit;

class ProductionUnitService extends AbstractService
{
    public function __construct(ProductionUnit $model)
    {
        parent::__construct($model);
    }
}
