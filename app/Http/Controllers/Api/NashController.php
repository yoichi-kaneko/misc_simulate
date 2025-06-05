<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Calculations\Nash;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateNashRequest;
use Illuminate\Http\JsonResponse;

class NashController extends Controller
{
    /**
     * Nash計算
     * @param CalculateNashRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function calculate(CalculateNashRequest $request): JsonResponse
    {
        $calculator = app()->make(Nash::class);
        try {
            $result = $calculator->run(
                $request->input('alpha_1'),
                $request->input('alpha_2'),
                $request->input('beta_1'),
                $request->input('beta_2'),
                $request->input('rho')
            );
        } catch (\Exception $e) {
            return $this->responseException($e->getMessage());
        }
        return response()->json($result);
    }
}
