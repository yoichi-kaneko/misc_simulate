<?php

namespace App\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateCentipedeRequest;

class CentipedeController extends Controller
{
    /**
     * Centipede計算
     * @param CalculateCentipedeRequest $request
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function calculate(CalculateCentipedeRequest $request): array
    {
        $case = (int) $request->get('case');
        $base_numerator = (int) $request->get('base_numerator');
        $numerator_exp_1 = (int) $request->get('numerator_exp_1');
        $numerator_exp_2 = (int) $request->get('numerator_exp_2');
        $denominator_exp = (int) $request->get('denominator_exp');
        $calculator = app()->make(Centipede::class);
        return $calculator->run(
            $case,
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
    }
}
