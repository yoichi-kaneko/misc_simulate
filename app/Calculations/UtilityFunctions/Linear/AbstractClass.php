<?php
namespace App\Calculations\UtilityFunctions\Linear;

abstract class AbstractClass
{
    /*
     * 効用値を計算する。結果は次の配列で返される。
     *
     * value: 計算結果の整数
     * display: 計算結果の表示テキスト。整数の他、数式を返す場合もある
     * expression: trueならばdisplayが整数である事を示す
     *
     */
    public static function run($cognitive_degree, $banker_budget_degree, $participation_fee, $prize_unit)
    {
        $lower_value = static::calculate_lower(
            $cognitive_degree,
            $banker_budget_degree,
            $participation_fee,
            $prize_unit
        );
        $upper_value = static::calculate_upper(
            $cognitive_degree,
            $banker_budget_degree,
            $participation_fee,
            $prize_unit
        );
        $display_expression = static::parse_expression(
            $lower_value,
            $upper_value,
            $cognitive_degree,
            $banker_budget_degree,
            $participation_fee,
            $prize_unit
        );

        return [
            'lower_value' => $lower_value,
            'upper_value' => $upper_value,
            'display_expression' => $display_expression,
        ];
    }

    abstract protected static function calculate_lower(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    );
    abstract protected static function calculate_upper(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    );

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
    abstract protected static function parse_expression(
        int $lower_value,
        int $upper_value,
        int $cognitive_degree,
        int $banker_budget_degree,
        int $participation_fee,
        int $prize_unit
    );

    /**
     * 結果に対して階乗表記を計算する
     * @param int $result_value 結果の数値
     * @param int $base_value 基底の数値
     * @param int $exp 階乗
     * @return string
     */
    protected static function parse_value_to_factorial($result_value, $base_value, $exp) {
        // 割り切れない場合は渡した値が正しくないのでfalse
        if ($result_value % $base_value > 0) {
            return false;
        }
        $coefficient = $result_value / $base_value;
        return (string)$coefficient . '	\bullet 2^{' . (string)$exp . '}';
    }
}
