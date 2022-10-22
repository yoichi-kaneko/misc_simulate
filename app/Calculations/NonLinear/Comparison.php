<?php

namespace App\Calculations\NonLinear;

use App\Calculations\Budget;
use App\Calculations\Comparison\ParticipationRate;
use App\Calculations\Comparison\Row;
use App\Calculations\Comparison\TotalVariance;
use App\Calculations\UtilityFunctions\NonLinear\Fee;
use App\Calculations\UtilityFunctions\NonLinear\SubjectiveUnderstanding;

class Comparison
{
    /** @var Fee */
    private $fee_calculator;

    /** @var SubjectiveUnderstanding */
    private $subjective_understanding_calculator;

    /**
     * Constructor.
     * @param Fee $fee_calculator
     * @param SubjectiveUnderstanding $subjective_understanding_calculator
     */
    public function __construct(
        Fee $fee_calculator,
        SubjectiveUnderstanding $subjective_understanding_calculator
    ) {
        $this->fee_calculator = $fee_calculator;
        $this->subjective_understanding_calculator = $subjective_understanding_calculator;
    }

    /**
     * 参加率を計算する
     * @param int $bankers_budget
     * @param int $participation_fee
     * @param int $prize_unit
     * @param int $potential_participants
     * @param array $cognitive_degrees_distribution
     * @param string $theta_function_formula
     * @return array
     * @throws \Exception
     */
    public function run(
        int $bankers_budget,
        int $participation_fee,
        int $prize_unit,
        int $potential_participants,
        array $cognitive_degrees_distribution,
        string $theta_function_formula
    ) :array {
        $budget_data = Budget::run($bankers_budget,  $prize_unit);
        $max_banker_budget_degree = config('simulate.max_banker_budget_degree');
        $cognitive_degrees_distribution = $this->make_cognitive_degrees_distribution(
            $max_banker_budget_degree,
            $cognitive_degrees_distribution
        );
        $theta_function_values = $this->make_theta_function($participation_fee, $budget_data['degree'], $theta_function_formula);

        $row = $this->make_row(
            $max_banker_budget_degree,
            $budget_data['degree'],
            $theta_function_values['rounded_theta_ast'],
            $prize_unit,
            $theta_function_formula
        );
        $participation_rate = $this->makeParticipationRate($cognitive_degrees_distribution, $row);
        $total_variance = $this->makeTotalVariance($potential_participants, $cognitive_degrees_distribution, $row);

        return [
            'cognitive_degrees_distribution' => $cognitive_degrees_distribution,
            'theta_function_values' => $theta_function_values,
            'row' => $row,
            'participation_rate' => $participation_rate,
            'banker_maximum_prize' => $this->parse_exponential_expression($budget_data['prize'], $prize_unit),
            'banker_budget_degree' => $budget_data['degree'],
            'potential_participants' => $potential_participants,
            'expected_participant_number' => (int) round($potential_participants * $participation_rate),
            'total_variance' => $total_variance,
        ];
    }

    /**
     * @param int $max_banker_budget_degree
     * @param array $cognitive_degrees_distribution
     * @return array
     */
    private function make_cognitive_degrees_distribution(
        int $max_banker_budget_degree, array $cognitive_degrees_distribution
    ) : array {
        $ret = [];
        for ($i = 0; $i <= $max_banker_budget_degree; $i++) {
            $ret[$i] = $cognitive_degrees_distribution[$i] ?? 0;
        }
        return $ret;
    }

    /**
     * セータ関数の結果を演算する
     * @param int $participation_fee
     * @param int $banker_budget_degree
     * @param string $theta_function_formula
     * @return array
     */
    private function make_theta_function(int $participation_fee, int $banker_budget_degree, string $theta_function_formula): array
    {
        return ThetaFunction::run($participation_fee, $banker_budget_degree, $theta_function_formula);
    }

    /**
     * @param int $max_banker_budget_degree
     * @param int $banker_budget_degree
     * @param int $theta_ast_value
     * @param int $prize_unit
     * @param string $theta_function_formula
     * @return array
     */
    private function make_row(
        int $max_banker_budget_degree,
        int $banker_budget_degree,
        int $theta_ast_value,
        int $prize_unit,
        string $theta_function_formula
    ): array {
        $ret = [];
        for ($i = 0; $i <= $max_banker_budget_degree; $i++) {
            $cognitive_degree = ($i <= $banker_budget_degree) ? $i : $banker_budget_degree;

            $utility_functions = $this->get_utility_functions(
                $cognitive_degree,
                $banker_budget_degree,
                $theta_ast_value,
                $prize_unit,
                $theta_function_formula
            );
            $comparison = $this->get_comparison_result($i, $utility_functions);

            $ret[$i] = [
                'cognitive_degree' => $i,
                'utility_functions' => $utility_functions,
                'comparison' => $comparison
            ];
        }
        return $ret;
    }

    /**
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $theta_function_value
     * @param int $prize_unit
     * @param string $theta_function_formula
     * @return array
     */
    private function get_utility_functions(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit,
        string $theta_function_formula
    ): array
    {
        return [
            'fee' => $this->fee_calculator->run(
                $cognitive_degree,
                $banker_budget_degree,
                $theta_function_value,
                $prize_unit
            ),
            'subjective_understanding' => $this->subjective_understanding_calculator->run(
                $cognitive_degree,
                $banker_budget_degree,
                $theta_function_value,
                $prize_unit,
                $theta_function_formula
            ),
        ];
    }

    private function get_comparison_result(
        int $cognitive_degree,
        array $utility_functions
    ): array {
        return Row::run(
            $cognitive_degree,
            $utility_functions['subjective_understanding']['lower_value'],
            $utility_functions['subjective_understanding']['upper_value'],
            $utility_functions['fee']['lower_value'],
            $utility_functions['fee']['upper_value']
        );
    }

    private function parse_exponential_expression(int $value, int $prize_unit): string
    {
        $i = 0;
        $amount = 1;

        while ($amount < $value / $prize_unit) {
            $i++;
            $amount *= 2;
        }
        return $prize_unit . ' \bullet 2^{' . $i . '} (= ' . number_format($value) . ')';
    }

    /**
     * 参加率を計算する
     * @param array $cognitive_degrees_distribution
     * @param array $row
     * @return float
     */
    private function makeParticipationRate(array $cognitive_degrees_distribution, array $row): float
    {
        $utility_function_rate = [];
        // 効用関数から、各cognitive_degreeの確率のテキストを取得する
        foreach ($row as $value) {
            $utility_function_rate[$value['cognitive_degree']] = $value['comparison']['display'];
        }
        return ParticipationRate::run($cognitive_degrees_distribution, $utility_function_rate);
    }

    /**
     * 分散を計算する
     * @param int $potential_participants
     * @param array $cognitive_degrees_distribution
     * @param array $row
     * @return float
     */
    private function makeTotalVariance(
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
}
