<?php
namespace App\Calculations;

class Budget
{
    /**
     * 予算から、最大の賞金額と最大の勝利回数を算出する
     * @param int $amount 元の金額
     * @param int $prize_unit 最初の賭け金
     */
    public static function run(int $amount, int $prize_unit)
    {
        $ret = [
            'prize' => $prize_unit,
            'degree' => 0,
            'degree_int' => 1
        ];

        while (($ret['prize'] * 2) <= $amount) {
            $ret['prize'] *= 2;
            $ret['degree']++;
            $ret['degree_int'] *= 2;
        }

        return $ret;
    }
}
