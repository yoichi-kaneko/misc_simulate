<?php
namespace App\Calculations\UtilityFunctions\NonLinear;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SubjectiveUnderstanding
{
    private const SUBJECTIVE_CELL = 'A1';
    private const ZERO_CELL = 'A2';
    private const EXP_CELL = 'A3';
    private const THETA_SUBJECTIVE_CELL = 'B1';
    private const THETA_ZERO_CELL = 'B2';
    private const THETA_EXP_CELL = 'B3';
    private const THETA_AST_CELL = 'B4';

    private const X_STRING = '[x]';
    private const THETA_AST_FORMULA = '=A3 * (B1 - B2) / (B3 - B2)';

    /** @var array 各cognitiveのbase_valueを保存 */
    private $base_value_for_each_cognitive = [];

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
     * @param string $theta_function_formula
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function run(
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit,
        string $theta_function_formula
    ): array {
        $lower_values = $this->calculateLowerValues(
            $cognitive_degree,
            $banker_budget_degree,
            $theta_function_formula,
            $prize_unit
        );
        $lower_value = array_sum($lower_values);

        $upper_values = $this->calculateUpperValues(
            $cognitive_degree,
            $banker_budget_degree,
            $theta_function_formula,
            $prize_unit
        );
        $upper_value = array_sum($upper_values);
        $display_expression = $this->parseExpression(
            $lower_value,
            $upper_value,
            $cognitive_degree,
            $banker_budget_degree,
            $theta_function_value,
            $prize_unit
        );
        $expanded_formula_expression = $this->parseExpandedExpression(
            $lower_values,
            $upper_values,
            $cognitive_degree,
            $banker_budget_degree,
            $prize_unit
        );

        return [
            'lower_value' => $lower_value,
            'upper_value' => $upper_value,
            'display_expression' => $display_expression,
            'expanded_formula_expression' => $expanded_formula_expression,
        ];
    }

    /**
     * 規定値を取得する
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param string $theta_function_formula
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    private function getBaseValue(
        int $cognitive_degree,
        int $banker_budget_degree,
        string $theta_function_formula
    ) {
        if(isset($this->base_value_for_each_cognitive[$cognitive_degree])) {
            return $this->base_value_for_each_cognitive[$cognitive_degree];
        }
        $obj_spread_sheet = new Spreadsheet();
        $obj_style_sheet = $obj_spread_sheet->getActiveSheet();

        // 値のセット
        $subjective_value = pow(2, $cognitive_degree);
        $obj_style_sheet->setCellValue(self::SUBJECTIVE_CELL, $subjective_value);
        $obj_style_sheet->setCellValue(self::ZERO_CELL, 0);

        // banker_budget_degreeの指数をセット
        $exponential = pow(2, $banker_budget_degree);
        $obj_style_sheet->setCellValue(self::EXP_CELL, $exponential);
        $formula = '=' . $theta_function_formula;

        // 関数として、xにFeeと0を代入した式をセット
        $b1_formula = str_replace(self::X_STRING, self::SUBJECTIVE_CELL, $formula);
        $b2_formula = str_replace(self::X_STRING, self::ZERO_CELL, $formula);
        $b3_formula = str_replace(self::X_STRING, self::EXP_CELL, $formula);
        $obj_style_sheet->setCellValue(self::THETA_SUBJECTIVE_CELL, $b1_formula);
        $obj_style_sheet->setCellValue(self::THETA_ZERO_CELL, $b2_formula);
        $obj_style_sheet->setCellValue(self::THETA_EXP_CELL, $b3_formula);
        $obj_style_sheet->setCellValue(self::THETA_AST_CELL, self::THETA_AST_FORMULA);

        $this->base_value_for_each_cognitive[$cognitive_degree] = $obj_style_sheet->getCell(self::THETA_AST_CELL)->getCalculatedValue();

        unset($obj_spread_sheet, $obj_style_sheet);
        return $this->base_value_for_each_cognitive[$cognitive_degree];
    }

    /**
     * 効用関数の上方値を計算する
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param string $theta_function_formula ,
     * @param int $prize_unit
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    private function calculateUpperValues(
        int $cognitive_degree,
        int $banker_budget_degree,
        string $theta_function_formula,
        int $prize_unit
    ) :array {
        if ($cognitive_degree === 0) {
            return [0];
        }
        $return_values = [];
        for ($i = 1; $i <= $cognitive_degree; $i++) {
            $base_value = $this->getBaseValue($i, $banker_budget_degree, $theta_function_formula);

            $exp = $banker_budget_degree - $cognitive_degree;
            $base_pow = pow(2, $exp);
            $split_exp = pow(2, $i);
            $return_values[$i] = $base_pow * $prize_unit * (int) ceil($base_value / $base_pow / $split_exp);
        }

        return $return_values;
    }

    /**
     * 効用関数の下方値を計算する
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param string $theta_function_formula
     * @param int $prize_unit
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    private function calculateLowerValues(
        int $cognitive_degree,
        int $banker_budget_degree,
        string $theta_function_formula,
        int $prize_unit
    ) :array {
        if ($cognitive_degree === 0) {
            return [0];
        }
        $return_values = [];
        for ($i = 1; $i <= $cognitive_degree; $i++) {
            $base_value = $this->getBaseValue($i, $banker_budget_degree, $theta_function_formula);

            $exp = $banker_budget_degree - $cognitive_degree;
            $base_pow = pow(2, $exp);
            $split_exp = pow(2, $i);
            $return_values[$i] = $base_pow * $prize_unit * (int) floor($base_value / $base_pow / $split_exp);
        }
        return $return_values;
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
    private function parseExpression(
        int $lower_value,
        int $upper_value,
        int $cognitive_degree,
        int $banker_budget_degree,
        int $theta_function_value,
        int $prize_unit
    ): string {
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_value = pow(2, $exp);

        if ($this->useLowerExpression($lower_value, $base_value)) {
            $lower_text = $this->parse_value_to_factorial($lower_value / $prize_unit, $base_value, $exp);
        } else {
            $lower_text = (string)$lower_value / $prize_unit;
        }
        if ($this->useUpperExpression($cognitive_degree, $banker_budget_degree)) {
            $upper_text = $this->parse_value_to_factorial($upper_value / $prize_unit, $base_value, $exp);
        } else {
            $upper_text = (string)$upper_value / $prize_unit;
        }

        return $prize_unit . '\ \left[ ' . $upper_text . '\ ;\ ' . $lower_text . ' \right]';
    }

    /**
     * 計算した値を数式に変換する（各項を展開した状態）
     * @param array $lower_values
     * @param array $upper_values
     * @param int $cognitive_degree
     * @param int $banker_budget_degree
     * @param int $prize_unit
     * @return string
     */
    private function parseExpandedExpression(
        array $lower_values,
        array $upper_values,
        int $cognitive_degree,
        int $banker_budget_degree,
        int $prize_unit
    ): string
    {
        // 項目数が1件の場合には展開しないので合計値のExpression出力を行う
        if (count($lower_values) === 1) {
            return $this->parseExpression(
                current($lower_values),
                current($upper_values),
                $cognitive_degree,
                $banker_budget_degree,
                0,
                $prize_unit
            );
        }
        $exp = $banker_budget_degree - $cognitive_degree;
        $base_value = pow(2, $exp);
        $each_expressions = [];

        foreach ($lower_values as $i => $lower_value) {
            $upper_value = $upper_values[$i];
            if ($this->useLowerExpression($lower_value, $base_value)) {
                $lower_text = $this->parse_value_to_factorial($lower_value / $prize_unit, $base_value, $exp);
            } else {
                $lower_text = (string)$lower_value / $prize_unit;
            }
            if ($this->useUpperExpression($cognitive_degree, $banker_budget_degree)) {
                $upper_text = $this->parse_value_to_factorial($upper_value / $prize_unit, $base_value, $exp);
            } else {
                $upper_text = (string)$upper_value / $prize_unit;
            }
            $each_expressions[] = '\left[ ' . $upper_text . '\ ;\ ' . $lower_text . ' \right]';
        }
        $joined_expression = implode('\ +\ ', $each_expressions);

        return $prize_unit . '\lparen' . $joined_expression . '\rparen';
    }

    private function useLowerExpression(
        int $lower_value,
        int $base_value
    ) {
        if ($lower_value === 0) {
            return false;
        }
        if ($lower_value % $base_value === 0) {
            return true;
        }
        return false;
    }

    private function useUpperExpression(
        $cognitive_degree,
        $banker_budget_degree
    ){
        if ($cognitive_degree === 0 || $cognitive_degree >= $banker_budget_degree) {
            return false;
        }
        return true;
    }


    /**
     * 結果に対して階乗表記を計算する
     * @param int $result_value 結果の数値
     * @param int $base_value 基底の数値
     * @param int $exp 階乗
     * @return string
     */
    private function parse_value_to_factorial(int $result_value, int $base_value, int $exp): string
    {
        // 割り切れない場合は渡した値が正しくないのでfalse
        if ($result_value % $base_value > 0) {
            return false;
        }
        $coefficient = $result_value / $base_value;
        return (string)$coefficient . '	\bullet 2^{' . (string)$exp . '}';
    }
}
