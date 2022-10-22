<?php
namespace App\Calculations\UtilityFunctions\NonLinear;

class Fee
{
    /*
     * 効用値を計算する。結果は次の配列で返される。
     *
     * value: 計算結果の整数
     * display: 計算結果の表示テキスト。整数の他、数式を返す場合もある
     * expression: trueならばdisplayが整数である事を示す
     *
     */

    /**
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $theta_function_value
     * @param int $prize_unit
     * @return array
     */
    public function run(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit
    ): array {
        $lower_value = $this->calculate_lower(
            $cognitive_degree,
            $banker_budget_degree,
            $theta_function_value,
            $prize_unit
        );
        $upper_value = $this->calculate_upper(
            $cognitive_degree,
            $banker_budget_degree,
            $theta_function_value,
            $prize_unit
        );
        $display_expression = $this->parse_expression(
            $lower_value,
            $upper_value,
            $cognitive_degree,
            $banker_budget_degree,
            $theta_function_value,
            $prize_unit
        );

        return [
            'lower_value' => $lower_value,
            'upper_value' => $upper_value,
            'display_expression' => $display_expression,
        ];
    }

    /**
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $theta_function_value
     * @param int $prize_unit
     * @return float|int
     */
    public function calculate_upper(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit
    ) {
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($theta_function_value == 0) {
            return 0;
        }
        if ($base_pow > $theta_function_value) {
            return $base_pow * $prize_unit;
        }

        if ($theta_function_value % $base_pow > 0) {
            return $base_pow * $prize_unit * (int) ceil($theta_function_value / $base_pow);
        }

        return $theta_function_value * $prize_unit;
    }

    /**
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $theta_function_value
     * @param int $prize_unit
     * @return float|int
     */
    public function calculate_lower(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit
    ) {
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($base_pow > $theta_function_value) {
            return 0;
        }

        if ($theta_function_value % $base_pow > 0) {
            return $base_pow * $prize_unit * (int) floor($theta_function_value / $base_pow);
        }

        return $theta_function_value * $prize_unit;
    }

    /**
     * 計算した値を数式に変換する
     * @param int $lower_value
     * @param int $upper_value
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $theta_function_value
     * @param int $prize_unit
     * @return string|boolean
     */
    protected function parse_expression(
        int $lower_value,
        int $upper_value,
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit
    ){
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_value = pow(2, $exp);

        if ($this->use_lower_expression($cognitive_degree, $banker_budget_degree, $theta_function_value)) {
            $lower_text = $this->parse_value_to_factorial($lower_value / $prize_unit, $base_value, $exp);
        } else {
            $lower_text = (string)$lower_value / $prize_unit;
        }
        if ($this->use_upper_expression($cognitive_degree, $banker_budget_degree, $theta_function_value)) {
            $upper_text = $this->parse_value_to_factorial($upper_value / $prize_unit, $base_value, $exp);
        } else {
            $upper_text = (string)$upper_value / $prize_unit;
        }

        return $prize_unit . '\ \left[ ' . $upper_text . '\ ;\ ' . $lower_text . ' \right]';
    }

    private function use_lower_expression(
        $cognitive_degree,
        $banker_budget_degree,
        $theta_function_value
    ){
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($base_pow > $theta_function_value) {
            return false;
        }

        if ($theta_function_value % $base_pow > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $cognitive_degree
     * @param $banker_budget_degree
     * @param $theta_function_value
     * @return bool
     */
    private function use_upper_expression(
        $cognitive_degree,
        $banker_budget_degree,
        $theta_function_value
    ){
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_pow = pow(2, $exp);

        if ($base_pow > $theta_function_value) {
            return true;
        }

        if ($theta_function_value % $base_pow > 0) {
            return true;
        }

        return false;
    }

    /**
     * 結果に対して階乗表記を計算する
     * @param int $result_value 結果の数値
     * @param int $base_value 基底の数値
     * @param int $exp 階乗
     * @return string
     */
    protected function parse_value_to_factorial($result_value, $base_value, $exp) {
        // 割り切れない場合は渡した値が正しくないのでfalse
        if ($result_value % $base_value > 0) {
            return false;
        }
        $coefficient = $result_value / $base_value;
        return (string)$coefficient . '	\bullet 2^{' . (string)$exp . '}';
    }

}
