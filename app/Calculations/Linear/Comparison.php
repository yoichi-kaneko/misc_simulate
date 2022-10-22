<?php

namespace App\Calculations\Linear;

use App\Calculations\Budget;
use App\Calculations\Comparison\ParticipationRate;
use App\Calculations\Comparison\TotalVariance;
use App\Calculations\UtilityFunctions\Linear\SubjectiveUnderstanding;
use App\Calculations\UtilityFunctions\Linear\Fee;
use App\Calculations\Comparison\Row;

class Comparison
{
    /**
     * 参加率を計算する
     * @param int $bankers_budget
     * @param int $participation_fee
     * @param int $prize_unit
     * @param int $potential_participants
     * @param array $cognitive_degrees_distribution
     * @return array
     */
    public static function run(
        int $bankers_budget,
        int $participation_fee,
        int $prize_unit,
        int $potential_participants,
        array $cognitive_degrees_distribution
    ) :array {
        $budget_data = Budget::run($bankers_budget,  $prize_unit);
        $max_banker_budget_degree = config('simulate.max_banker_budget_degree');
        $cognitive_degrees_distribution = self::make_cognitive_degrees_distribution(
            $max_banker_budget_degree,
            $cognitive_degrees_distribution
        );
        $row = self::make_row($max_banker_budget_degree, $budget_data['degree'], $participation_fee, $prize_unit);
        $participation_rate = self::make_participation_rate($cognitive_degrees_distribution, $row);
        $total_variance = self::make_total_variance($potential_participants, $cognitive_degrees_distribution, $row);

        return [
            'cognitive_degrees_distribution' => $cognitive_degrees_distribution,
            'row' => $row,
            'participation_rate' => $participation_rate,
            'banker_maximum_prize' => self::parse_exponential_expression($budget_data['prize'], $prize_unit),
            'banker_budget_degree' => $budget_data['degree'],
            'potential_participants' => $potential_participants,
            'expected_participant_number' => (int) round($potential_participants * $participation_rate),
            'total_variance' => $total_variance
        ];
    }

    private static function make_cognitive_degrees_distribution(
        int $max_banker_budget_degree, array $cognitive_degrees_distribution
    ) : array {
        $ret = [];
        for ($i = 0; $i <= $max_banker_budget_degree; $i++) {
            $ret[$i] = $cognitive_degrees_distribution[$i] ?? 0;
        }
        return $ret;
    }

    private static function make_row(
        int $max_banker_budget_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ): array {
        $ret = [];
        for ($i = 0; $i <= $max_banker_budget_degree; $i++) {
            $cognitive_degree = ($i <= $banker_budget_degree) ? $i : $banker_budget_degree;

            $utility_functions = self::get_utility_functions(
                $cognitive_degree,
                $banker_budget_degree,
                $participation_fee,
                $prize_unit
            );
            $comparison = self::get_comparison_result($i, $utility_functions);

            $ret[$i] = [
                'cognitive_degree' => $i,
                'utility_functions' => $utility_functions,
                'comparison' => $comparison
            ];
        }
        return $ret;
    }

    private static function make_participation_rate(array $cognitive_degrees_distribution, array $row)
    {
        $utility_function_rate = [];
        // 効用関数から、各cognitive_degreeの確率のテキストを取得する
        foreach ($row as $value) {
            $utility_function_rate[$value['cognitive_degree']] = $value['comparison']['display'];
        }
        return ParticipationRate::run($cognitive_degrees_distribution, $utility_function_rate);
    }

    /**
     * @param int $potential_participants
     * @param array $cognitive_degrees_distribution
     * @param array $row
     * @return float
     */
    private static function make_total_variance(
        int $potential_participants,
        array $cognitive_degrees_distribution,
        array $row
    ): float {
        $utility_function_rate = [];
        // 効用関数から、各cognitive_degreeの確率のテキストを取得する
        foreach ($row as $value) {
            $utility_function_rate[$value['cognitive_degree']] = $value['comparison']['display'];
        }
        return TotalVariance::run($potential_participants, $cognitive_degrees_distribution, $utility_function_rate);
    }

    private static function get_utility_functions(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ): array
    {
        return [
            'subjective_understanding' => SubjectiveUnderstanding::run(
                $cognitive_degree,
                $banker_budget_degree,
                $participation_fee,
                $prize_unit
            ),
            'fee' => Fee::run(
                $cognitive_degree,
                $banker_budget_degree,
                $participation_fee,
                $prize_unit
            ),
        ];
    }

    private static function get_comparison_result(
        int $cognitive_degree,
        array $utility_functions
    ): array
    {
        return Row::run(
            $cognitive_degree,
            $utility_functions['subjective_understanding']['lower_value'],
            $utility_functions['subjective_understanding']['upper_value'],
            $utility_functions['fee']['lower_value'],
            $utility_functions['fee']['upper_value']
        );
    }

    private static function parse_exponential_expression(int $value, int $prize_unit): string
    {
        $i = 0;
        $amount = 1;

        while ($amount < $value / $prize_unit) {
            $i++;
            $amount *= 2;
        }
        return $prize_unit . ' \bullet 2^{' . $i . '} (= ' . number_format($value) . ')';
    }
}
