<?php

namespace App\Calculations;

use http\Encoding\Stream\Inflate;

/**
 * Centipede計算を行うシミュレーター
 */
class Centipede
{
    private const MAX_COUNT = 148;
    private const DELTA_VALUE = 300 / 256;
    private const ODD_NU = 'floor((2 * %1$d - 1) / %2$f)';
    private const ODD_LEFT_SIDE = '2 * %1$d';
    private const ODD_RIGHT_SIDE = '((%2$d + 1) * %1$f)';
    private const EVEN_NU = 'floor((2 * %1$d + 2) / %2$f)';
    private const EVEN_LEFT_SIDE = '2 * %1$d + 3';
    private const EVEN_RIGHT_SIDE = '((%2$d + 1) * %1$f)';

    /**
     * 計算を実行する
     * @return array
     */
    public function run(): array
    {
        $data = [];
        for ($i = 1; $i <= self::MAX_COUNT; $i++) {
            if ($i % 2 > 0) {
                $max_nu_formula = self::ODD_NU;
                $left_side_formula = self::ODD_LEFT_SIDE;
                $right_side_formula = self::ODD_RIGHT_SIDE;
            } else {
                $max_nu_formula = self::EVEN_NU;
                $left_side_formula = self::EVEN_LEFT_SIDE;
                $right_side_formula = self::EVEN_RIGHT_SIDE;
            }
            /*
             * 次の手順で計算を行う。
             * - 計算式1で、nuの最大値を求める
             * - 計算式2の左辺と右辺を求める
             * - 右辺の方が大きい場合、結果はtrue
             */
            $max_nu_value = $this->evalFormula(sprintf($max_nu_formula, $i, self::DELTA_VALUE));
            $left_side_value = $this->evalFormula(sprintf($left_side_formula, $i));
            $right_side_value = $this->evalFormula(sprintf($right_side_formula, self::DELTA_VALUE, $max_nu_value));
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