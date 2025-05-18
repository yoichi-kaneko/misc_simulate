<?php

namespace App\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateNashRequest;
use Illuminate\Http\JsonResponse;

class NashController extends Controller
{
    /**
     * Nash計算
     * @param CalculateNashRequest $request
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function calculate(CalculateNashRequest $request): JsonResponse
    {
        // 仮置き。ここに計算処理を入れる
        return response()->json(['result' => 'success']);
        /*
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

        return response()->json($result);*/
    }
}
