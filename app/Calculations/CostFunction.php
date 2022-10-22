<?php
namespace App\Calculations;

/**
 * Class CostFunction
 * @package App\Calculations
 */
class CostFunction
{
    /**
     * コストを計算する
     * @param int $initial_setup_cost
     * @param int $facility_unit_cost
     * @param int $facility_unit
     * @param int $participants
     * @return int
     */
    public static function run(int $initial_setup_cost, int $facility_unit_cost, int $facility_unit, int $participants)
    {
        return $initial_setup_cost + (int)(ceil($participants / $facility_unit)) * $facility_unit_cost;
    }
}
