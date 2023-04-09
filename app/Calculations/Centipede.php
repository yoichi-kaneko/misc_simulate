<?php

namespace App\Calculations;

use Illuminate\Support\Arr;

/**
 * Centipede計算を行うシミュレーター
 */
class Centipede
{
    // NUの値は「所定の計算式の値より小さい整数」のため、計算式が整数ちょうどになった場合を考慮してceil() - 1という式としている
    private const ODD_NU = 'ceil(pow(2 * %1$d - 1, %3$d / %4$d) / %2$.15f) - 1';
    private const ODD_LEFT_SIDE = 'number_format(pow(2 * %1$d, %2$d / %3$d), 8)';
    private const ODD_RIGHT_SIDE = 'number_format((%2$d + 1) * %1$.15f, 8)';
    private const EVEN_NU = 'ceil(pow(2 * %1$d + 2, %3$d / %4$d) / %2$.15f) - 1';
    private const EVEN_LEFT_SIDE = 'number_format(pow(2 * %1$d + 3, %2$d / %3$d), 8)';
    private const EVEN_RIGHT_SIDE = 'number_format((%2$d + 1) * %1$.15f, 8)';

    /**
     * 計算を実行する
     * @param array $patterns
     * @param int $max_step
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException|\Exception
     */
    public function run(
        array $patterns,
        int $max_step
    ): array {
        $pattern_data = [];

        foreach ($patterns as $key => $pattern_val) {
            $pattern_result = $this->calculatePattern(
                (int) $pattern_val['base_numerator'],
                (int) $pattern_val['numerator_exp_1'],
                (int) $pattern_val['numerator_exp_2'],
                (int) $pattern_val['denominator_exp'],
                $max_step
            );
            $pattern_data[$key] = $pattern_result;
        }

        return [
            'result' => 'ok',
            'pattern_data' => $pattern_data,
        ];
    }

    /**
     * それぞれのパターンについて計算を行う
     * @param int $base_numerator
     * @param int $numerator_exp_1
     * @param int $numerator_exp_2
     * @param int $denominator_exp
     * @param int $max_step
     * @return array
     * @throws \Exception
     */
    private function calculatePattern(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp,
        int $max_step
    ): array {
        $cognitive_unit_value = $this->calcCognitiveUnitValue(
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );

        for ($i = 1; $i <= $max_step; $i++) {
            /*
             * 次の手順で計算を行う。
             * - 計算式1で、nuの最大値を求める
             * - 計算式2の左辺と右辺を求める
             * - 右辺の方が大きい場合、結果はtrue
             */
            if ($i % 2 > 0) {
                $max_nu_formula = self::ODD_NU;
                $left_side_formula = self::ODD_LEFT_SIDE;
                $right_side_formula = self::ODD_RIGHT_SIDE;
            } else {
                $max_nu_formula = self::EVEN_NU;
                $left_side_formula = self::EVEN_LEFT_SIDE;
                $right_side_formula = self::EVEN_RIGHT_SIDE;
            }

            $max_nu_value = $this->evalFormula(
                sprintf(
                    $max_nu_formula,
                    $i,
                    $cognitive_unit_value,
                    $numerator_exp_1,
                    $numerator_exp_2
                )
            );
            $left_side_value = $this->evalFormula(
                sprintf(
                    $left_side_formula,
                    $i,
                    $numerator_exp_1,
                    $numerator_exp_2
                )
            );
            $right_side_value = $this->evalFormula(
                sprintf(
                    $right_side_formula,
                    $cognitive_unit_value,
                    $max_nu_value
                )
            );
            $data[] =[
                't' => $i,
                'max_nu_value' => $max_nu_value,
                'left_side_value' => $left_side_value,
                'right_side_value' => $right_side_value,
                'result' => ($left_side_value < $right_side_value),
            ];
        }
        $chart_data = $this->makeChartData($data);
        $cognitive_unit_latex_text = $this->makeCognitiveUnitLatexText(
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
        $average_of_reversed_causality = (array_sum(Arr::pluck($chart_data, 'y')) / count($chart_data));

        return [
            'cognitive_unit_latex_text' => $cognitive_unit_latex_text,
            'cognitive_unit_value' => $cognitive_unit_value,
            'average_of_reversed_causality' => $average_of_reversed_causality,
            'data' => $data,
            'chart_data' => $chart_data,
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

    /**
     * Cognitive Unitの値を計算して返す
     * @param int $base_numerator
     * @param int $numerator_exp_1
     * @param int $numerator_exp_2
     * @param int $denominator_exp
     * @return float
     * @throws \Exception
     */
    private function calcCognitiveUnitValue(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp
    ): float {
        $numerator = pow($base_numerator, ($numerator_exp_1 / $numerator_exp_2));

        if(is_nan($numerator) || is_infinite($numerator)) {
            throw new \Exception(trans('validation.invalid_cognitive_unit'));
        }

        $denominator = pow(2, $denominator_exp);
        return $numerator / $denominator;
    }

    /**
     * Cognitive UnitのLatex形式のテキストを返す
     * @param int $base_numerator
     * @param int $numerator_exp_1
     * @param int $numerator_exp_2
     * @param int $denominator_exp
     * @return string
     */
    private function makeCognitiveUnitLatexText(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp
    ):string {
        $format = '\dfrac{%d^{\frac{%d}{%d}}}{2^{%d}}';

        return sprintf(
            $format,
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
    }

    /**
     * チャート用のデータを生成する
     * @param array $data
     * @return array
     */
    private function makeChartData(array $data): array
    {
        $chart_data = [];
        $last_skipped_t = 0;

        foreach ($data as $value) {
            // resultがtrueのデータが出た場合、それを最後にスキップしたtとして値を保存する。
            if ($value['result'] === true) {
                $last_skipped_t = $value['t'];
            }
            // スキップしたtが一度も出ていない間は、yはt - 1に等しい。
            if ($last_skipped_t === 0) {
                $y = $value['t'] - 1;
            // スキップしたtが出た場合、スキップした点を起点(0)として、そこから1ずつインクリメントしていく。
            } else {
                $y = $value['t'] - $last_skipped_t;
            }
            $chart_data[] = [
                'x' => $value['t'],
                'y' => $y,
            ];
        }

        return $chart_data;
    }
}