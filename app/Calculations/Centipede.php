<?php

namespace App\Calculations;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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
     * @param int|null $max_rc
     * @param array|null $combination_player_1
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException|\Exception
     */
    public function run(
        array $patterns,
        int $max_step,
        ?int $max_rc,
        ?array $combination_player_1
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
        if (!is_null($combination_player_1)) {
            $combination_data = $this->unionCalculateData($combination_player_1, $pattern_data);
        } else {
            $combination_data = null;
        }

        return [
            'result' => 'ok',
            'render_params' => [
                'max_step' => $max_step,
                'max_rc' => $max_rc,
            ],
            'pattern_data' => $pattern_data,
            'combination_data' => $combination_data,
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
        $data = [];
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
     * 2つのシミュレート結果を合算する
     * @param array $combination_player_1
     * @param array $pattern_data
     * @return array
     */
    private function unionCalculateData(array $combination_player_1, array $pattern_data): array
    {
        $combination_data = [];

        foreach ($combination_player_1 as $combination_player_key => $combination_player_val) {
            // Requestのバリデーションで $combination_player_val には1,2いずれかがセットされていると想定
            $pattern_data_1 = $pattern_data[$combination_player_key . '_1']['data'];
            $pattern_data_2 = $pattern_data[$combination_player_key . '_2']['data'];
            $player_1_is_1 = ($combination_player_val === '1');

            $data = [];
            $max_count = count($pattern_data_1);
            for ($i = 0; $i < $max_count; $i++) {
                // Player1が1で$iが偶数（0から始まるため）、Player1が2で$iが奇数の場合にAをセット
                if (
                    $player_1_is_1 && $i %2 === 0 ||
                    !$player_1_is_1 && $i %2 > 0
                ) {
                    $data[] = $pattern_data_1[$i];
                } else {
                    $data[] = $pattern_data_2[$i];
                }
            }
            $chart_data = $this->makeChartData($data);

            $average_of_reversed_causality = (array_sum(Arr::pluck($chart_data, 'y')) / count($chart_data));

            $combination_data[$combination_player_key] = [
                'data' => $data,
                'chart_data' => $chart_data,
                'cognitive_unit_latex_text_1' => $pattern_data[$combination_player_key . '_1']['cognitive_unit_latex_text'],
                'cognitive_unit_latex_text_2' => $pattern_data[$combination_player_key . '_2']['cognitive_unit_latex_text'],
                'cognitive_unit_value_1' => $pattern_data[$combination_player_key . '_1']['cognitive_unit_value'],
                'cognitive_unit_value_2' => $pattern_data[$combination_player_key . '_2']['cognitive_unit_value'],
                'average_of_reversed_causality' => $average_of_reversed_causality,
            ];
        }

        return $combination_data;
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

        // result中にtrueが1件でもあればyは0から開始する。ない場合は1。
        $results = Arr::pluck($data, 'result');
        $y_offset = in_array(true, $results) ? 0 : 1;

        foreach ($data as $value) {
            // resultがtrueのデータが出た場合、それを最後にスキップしたtとして値を保存する。
            if ($value['result'] === true) {
                $last_skipped_t = $value['t'];
            }
            // スキップしたtが一度も出ていない間は、yはt - 1に等しい。
            if ($last_skipped_t === 0) {
                $y = $value['t'] - 1 + $y_offset;
            // スキップしたtが出た場合、スキップした点を起点(0)として、そこから1ずつインクリメントしていく。
            } else {
                $y = $value['t'] - $last_skipped_t + $y_offset;
            }
            $chart_data[] = [
                'x' => $value['t'],
                'y' => $y,
            ];
        }

        return $chart_data;
    }
}