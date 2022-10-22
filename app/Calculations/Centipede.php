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
    private const ODD_FORMULA_1 = 'floor((2 * %1$d - 1) / %2$f)';
    private const ODD_FORMULA_2 = '(2 * %1$d) < ((%3$d + 1) * %2$f)';
    private const EVEN_FORMULA_1 = 'floor((2 * %1$d + 2) / %2$f)';
    private const EVEN_FORMULA_2 = '(2 * %1$d + 3) < ((%3$d + 1) * %2$f)';

    /**
     * 計算を実行する
     * @return array
     */
    public function run(): array
    {
        $ret = [];
        for ($i = 1; $i <= self::MAX_COUNT; $i++) {
            if ($i % 2 > 0) {
                $formula_1 = self::ODD_FORMULA_1;
                $formula_2 = self::ODD_FORMULA_2;
            } else {
                $formula_1 = self::EVEN_FORMULA_1;
                $formula_2 = self::EVEN_FORMULA_2;
            }
            /*
             * 次の手順で計算を行う。
             * - 計算式1で、nuの最大値を求める
             * - 計算式2で、nuの最大値をセットした場合に条件をするか判定する。
             */
            $max_nu_value = $this->evalFormula(sprintf($formula_1, $i, self::DELTA_VALUE));
            $result = $this->evalFormula(sprintf($formula_2, $i, self::DELTA_VALUE, $max_nu_value));
            $ret[$i] =[
                'max_nu_value' => $max_nu_value,
                'result' => $result,
            ];
        }
        return $ret;
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