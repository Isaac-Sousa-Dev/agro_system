<?php

namespace App\Services;

use App\Services\AbstractService;
use App\Models\Property;

class PropertyService extends AbstractService
{
    public function __construct(Property $model)
    {
        parent::__construct($model);
    }
}
