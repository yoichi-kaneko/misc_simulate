<?php

namespace App\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateCentipedeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
        $union_player_1 = $request->input('union_player_1', null);
        $calculator = app()->make(Centipede::class);

        try {
            $result = $calculator->run(
                $patterns,
                $max_step,
                $max_rc,
                $union_player_1
            );
        } catch (\Exception $e) {
            return $this->responseException($e->getMessage());
        }

        return response()->json($result);
    }
}
