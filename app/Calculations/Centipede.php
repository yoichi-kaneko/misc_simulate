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
     * @param int $denominator_exp デルタの分母の指数
     * @return array
     */
    public function run(int $case, int $denominator_exp): array
    {
        $data = [];

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
        return [
            'result' => 'ok',
            'delta_denominator' => pow(2, $denominator_exp),
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
}