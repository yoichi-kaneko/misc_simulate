<?php
namespace App\Calculations;

/**
 * Class Roi
 * @package App\Calculations
 */
class Roi
{
    /**
     * ROI(Return On Investment)を計算する
     * @param int $banker_prepared_change
     * @param int $current_cache
     * @param int $banker_budget
     * @param int $cost
     * @return float
     */
    public static function run(int $banker_prepared_change, int $current_cache, int $banker_budget, int $cost): float
    {
        $float = ($current_cache - $banker_prepared_change - $cost)
            / ($banker_budget + $banker_prepared_change + $cost);
        return round($float, 3);
    }
}
