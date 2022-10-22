<?php
namespace App\Calculations\UtilityFunctions\Linear;

class SubjectiveUnderstanding extends AbstractClass
{
    public static function calculate_upper(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ) {
        if ($cognitive_degree === 0) {
            return 0;
        }
        if ($cognitive_degree >= $banker_budget_degree) {
            return $prize_unit * $banker_budget_degree;
        }
        $exp = $banker_budget_degree - $cognitive_degree;
        return pow(2, $exp) * $prize_unit * $cognitive_degree;
    }

    public static function calculate_lower(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    ) {
        if ($cognitive_degree >= $banker_budget_degree) {
            return $prize_unit * $banker_budget_degree;
        }
        return 0;
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

        $lower_text = (string)$lower_value / $prize_unit;
        if (self::use_upper_expression($cognitive_degree, $banker_budget_degree)) {
            $upper_text = static::parse_value_to_factorial($upper_value / $prize_unit, $base_value, $exp);
        } else {
            $upper_text = (string)$upper_value / $prize_unit;
        }

        return $prize_unit . '\ \left[ ' . $upper_text . '\ ;\ ' . $lower_text . ' \right]';
    }

    private static function use_upper_expression(
        $cognitive_degree,
        $banker_budget_degree
    ){
        if ($cognitive_degree === 0 || $cognitive_degree >= $banker_budget_degree) {
            return false;
        }
        return true;
    }
}
