<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\Formatter;

use App\Calculations\Centipede\DTO\CentipedeSimulationResult;
use Illuminate\Support\Arr;

/**
 * Centipedeシミュレーション結果のフォーマッタ
 */
class CentipedeFormatter
{
    /**
     * シミュレーション結果をフロントエンド用に整形する
     * @param CentipedeSimulationResult $result
     * @return array
     */
    public function format(CentipedeSimulationResult $result): array
    {
        return [
            'cognitive_unit_latex_text' => $result->getCognitiveUnitLatexText(),
            'cognitive_unit_value' => $result->getCognitiveUnitValue(),
            'average_of_reversed_causality' => $result->getAverageOfReversedCausality(),
            'data' => $result->getData(),
            'chart_data' => $result->getChartData(),
        ];
    }

    /**
     * Cognitive UnitのLatex形式のテキストを生成する
     * @param int $baseNumerator
     * @param int $numeratorExp1
     * @param int $numeratorExp2
     * @param int $denominatorExp
     * @return string
     */
    public function makeCognitiveUnitLatexText(
        int $baseNumerator,
        int $numeratorExp1,
        int $numeratorExp2,
        int $denominatorExp
    ): string {
        $format = '\dfrac{%d^{\frac{%d}{%d}}}{2^{%d}}';

        return sprintf(
            $format,
            $baseNumerator,
            $numeratorExp1,
            $numeratorExp2,
            $denominatorExp
        );
    }

    /**
     * チャート用のデータを生成する
     * @param array $data
     * @return array
     */
    public function makeChartData(array $data): array
    {
        $chartData = [];
        $lastSkippedT = 0;

        // result中にtrueが1件でもあればyは0から開始する。ない場合は1。
        $results = Arr::pluck($data, 'result');
        $yOffset = in_array(true, $results) ? 0 : 1;

        foreach ($data as $value) {
            // resultがtrueのデータが出た場合、それを最後にスキップしたtとして値を保存する。
            if ($value['result'] === true) {
                $lastSkippedT = $value['t'];
            }
            // スキップしたtが一度も出ていない間は、yはt - 1に等しい。
            if ($lastSkippedT === 0) {
                $y = $value['t'] - 1 + $yOffset;
                // スキップしたtが出た場合、スキップした点を起点(0)として、そこから1ずつインクリメントしていく。
            } else {
                $y = $value['t'] - $lastSkippedT + $yOffset;
            }
            $chartData[] = [
                'x' => $value['t'],
                'y' => $y,
            ];
        }

        return $chartData;
    }
}