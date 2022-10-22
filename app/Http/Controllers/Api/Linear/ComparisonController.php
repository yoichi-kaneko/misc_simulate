<?php

namespace App\Http\Controllers\Api\Linear;

use App\Http\Controllers\Controller;
use App\Http\Requests\Linear\CalculateComparison;
use App\Calculations\Linear\Comparison;

class ComparisonController extends Controller
{
    public function calculate(CalculateComparison $request): array
    {
        $post_data = $request->all();
        return ['result' => Comparison::run(
            $post_data['bankers_budget'],
            $post_data['participation_fee'],
            $post_data['prize_unit'],
            $post_data['potential_participants'],
            $post_data['cognitive_degrees_distribution']
        )];
    }

}
