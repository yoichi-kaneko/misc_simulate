<?php

namespace App\Calculations;

use App\CaseConst\Case1;
use App\CaseConst\Case2;

/**
 * Centipede計算を行うシミュレーター
 */
class Centipede
{
    private const MAX_COUNT = 148;

    /**
     * 計算を実行する
     * @param int $case
     * @param int $base_numerator
     * @param int $numerator_exp_1
     * @param int $numerator_exp_2
     * @param int $denominator_exp デルタの分母の指数
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function run(
        int $case,
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp
    ): array {
        $data = [];
        $cognitive_unit_value = $this->calcCognitiveUnitValue(
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );

        if ($case === 1) {
            $const = app()->make(Case1::class);

        } else {
            $const = app()->make(Case2::class);
        }

        for ($i = 1; $i <= self::MAX_COUNT; $i++) {
            if ($i % 2 > 0) {
                $max_nu_formula = $const::ODD_NU;
                $left_side_formula = $const::ODD_LEFT_SIDE;
                $right_side_formula = $const::ODD_RIGHT_SIDE;
            } else {
                $max_nu_formula = $const::EVEN_NU;
                $left_side_formula = $const::EVEN_LEFT_SIDE;
                $right_side_formula = $const::EVEN_RIGHT_SIDE;
            }
            /*
             * 次の手順で計算を行う。
             * - 計算式1で、nuの最大値を求める
             * - 計算式2の左辺と右辺を求める
             * - 右辺の方が大きい場合、結果はtrue
             */

            $delta_value = $const->get_delta_value($denominator_exp);
            $max_nu_value = $this->evalFormula(sprintf($max_nu_formula, $i, $delta_value));
            $left_side_value = $this->evalFormula(sprintf($left_side_formula, $i));
            $right_side_value = $this->evalFormula(sprintf($right_side_formula, $delta_value, $max_nu_value));
            $data[] =[
                't' => $i,
                'max_nu_value' => $max_nu_value,
                'left_side_value' => $left_side_value,
                'right_side_value' => $right_side_value,
                'result' => ($left_side_value < $right_side_value),
            ];
        }
        $cognitive_unit_latex_text = $this->makeCognitiveUnitLatexText(
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
        return [
            'result' => 'ok',
            'cognitive_unit_latex_text' => $cognitive_unit_latex_text,
            'cognitive_unit_value' => $cognitive_unit_value,
            'data' => $data,
        ];
    }

    /**
     * 計算式をevalで実行する
     * @param string $str
     * @return mixed
     */
    private function evalFormula(string $str)
    {
        return eval('return ' . $str . ';');
    }

    /**
     * Cognitive Unitの値を計算して返す
     * @param int $base_numerator
     * @param int $numerator_exp_1
     * @param int $numerator_exp_2
     * @param int $denominator_exp
     * @return float
     * @throws \Exception
     */
    private function calcCognitiveUnitValue(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp
    ): float {
        $numerator = pow($base_numerator, ($numerator_exp_1 / $numerator_exp_2));

        if(is_nan($numerator) || is_infinite($numerator)) {
            throw new \Exception(trans('validation.invalid_cognitive_unit'));
        }

        $denominator = pow(2, $denominator_exp);
        return $numerator / $denominator;
    }

    /**
     * Cognitive UnitのLatex形式のテキストを返す
     * @param int $base_numerator
     * @param int $numerator_exp_1
     * @param int $numerator_exp_2
     * @param int $denominator_exp
     * @return string
     */
    private function makeCognitiveUnitLatexText(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp
    ):string {
        $format = '\dfrac{%d^{\frac{%d}{%d}}}{2^{%d}}';

        return sprintf(
            $format,
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
    }
}