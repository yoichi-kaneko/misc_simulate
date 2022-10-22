<?php

namespace App\Http\Controllers\Api\NonLinear;

use App\Http\Controllers\Controller;
use App\Http\Requests\NonLinear\CalculateComparison;
use App\Calculations\NonLinear\Comparison;
use Illuminate\Http\JsonResponse;

class ComparisonController extends Controller
{
    public function calculate(CalculateComparison $request)
    {
        $post_data = $request->all();
        try {
            $comparison = app()->make(Comparison::class);
            // TODO: 例外処理をちゃんと整理したい
            return ['result' => $comparison->run(
                $post_data['bankers_budget'],
                $post_data['participation_fee'],
                $post_data['prize_unit'],
                $post_data['potential_participants'],
                $post_data['cognitive_degrees_distribution'],
                $post_data['theta_function_formula']
            )];
        } catch (\Exception $e) {
            return $this->responseException($e->getMessage());
        }
    }

}
