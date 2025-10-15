<?php

namespace App\Services;

use App\Services\AbstractService;
use App\Models\Herd;

class HerdService extends AbstractService
{
    public function __construct(Herd $model)
    {
        parent::__construct($model);
    }
}
