<?php

declare(strict_types=1);

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
        $patterns = $request->input('patterns');
        $max_step = (int) $request->input('max_step');
        $max_rc = $request->input('max_rc') ? (int) $request->input('max_rc') : null;
        $combination_player_1 = $request->input('combination_player_1', null);
        $calculator = app()->make(Centipede::class);

        try {
            $result = $calculator->run(
                $patterns,
                $max_step,
                $max_rc,
                $combination_player_1
            );
        } catch (\Exception $e) {
            return $this->responseException($e->getMessage());
        }

        return response()->json($result);
    }
}
