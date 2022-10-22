<?php

namespace App\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Http\Controllers\Controller;

class CentipedeController extends Controller
{
    /**
     * Centipede計算
     * @return array
     */
    public function calculate(): array
    {
        $calculator = app()->make(Centipede::class);
        return $calculator->run();
    }
}
