<?php

namespace App\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateCentipedeRequest;
use Illuminate\Http\JsonResponse;

class CentipedeController extends Controller
{
    /**
     * Centipede計算
     * @param CalculateCentipedeRequest $request
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function calculate(CalculateCentipedeRequest $request): JsonResponse
    {
        $base_numerator = (int) $request->get('base_numerator');
        $numerator_exp_1 = (int) $request->get('numerator_exp_1');
        $numerator_exp_2 = (int) $request->get('numerator_exp_2');
        $denominator_exp = (int) $request->get('denominator_exp');
        $chart_offset = (int) $request->get('chart_offset');
        $calculator = app()->make(Centipede::class);

        try {
            $result = $calculator->run(
                $base_numerator,
                $numerator_exp_1,
                $numerator_exp_2,
                $denominator_exp,
                $chart_offset
            );
        } catch (\Exception $e) {
            return $this->responseException($e->getMessage());
        }

        return response()->json($result);
    }
}
