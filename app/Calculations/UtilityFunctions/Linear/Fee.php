<?php
namespace App\Calculations\UtilityFunctions\Linear;

class Fee extends AbstractClass
{
    public static function calculate_upper(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ) {
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($participation_fee == 0) {
            return 0;
        }
        if ($base_pow > $participation_fee) {
            return $base_pow * $prize_unit;
        }

        if ($participation_fee % $base_pow > 0) {
            return $base_pow * $prize_unit * (int) ceil($participation_fee / $base_pow);
        }

        return $participation_fee * $prize_unit;
    }

    public static function calculate_lower(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ) {
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($base_pow > $participation_fee) {
            return 0;
        }

        if ($participation_fee % $base_pow > 0) {
            return $base_pow * $prize_unit * (int) floor($participation_fee / $base_pow);
        }

        return $participation_fee * $prize_unit;
    }

    /**
     * 計算した値を数式に変換する
     * @param int $lower_value
     * @param int $upper_value
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $participation_fee
     * @param int $prize_unit
     * @return string|boolean
     */
    protected static function parse_expression(
        int $lower_value,
        int $upper_value,
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ){
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_value = pow(2, $exp);

        if (self::use_lower_expression($cognitive_degree, $banker_budget_degree, $participation_fee)) {
            $lower_text = static::parse_value_to_factorial($lower_value / $prize_unit, $base_value, $exp);
        } else {
            $lower_text = (string)$lower_value / $prize_unit;
        }
        if (self::use_upper_expression($cognitive_degree, $banker_budget_degree, $participation_fee)) {
            $upper_text = static::parse_value_to_factorial($upper_value / $prize_unit, $base_value, $exp);
        } else {
            $upper_text = (string)$upper_value / $prize_unit;
        }

        return $prize_unit . '\ \left[ ' . $upper_text . '\ ;\ ' . $lower_text . ' \right]';
    }

    private static function use_lower_expression(
        $cognitive_degree,
        $banker_budget_degree,
        $participation_fee
    ){
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($base_pow > $participation_fee) {
            return false;
        }

        if ($participation_fee % $base_pow > 0) {
            return true;
        }

        return false;
    }

    private static function use_upper_expression(
        $cognitive_degree,
        $banker_budget_degree,
        $participation_fee
    ){
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($base_pow > $participation_fee) {
            return true;
        }

        if ($participation_fee % $base_pow > 0) {
            return true;
        }

        return false;
    }

}
