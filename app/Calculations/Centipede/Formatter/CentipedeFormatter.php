<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\Formatter;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use App\Calculations\Centipede\DTO\CentipedeChartPointList;
use App\Calculations\Centipede\DTO\CentipedeSimulationResultInterface;
use App\Calculations\Centipede\DTO\CentipedeSimulationStepInterface;
use App\Traits\ArrayTypeCheckTrait;

/**
 * Centipedeシミュレーション結果のフォーマッタ
 */
class CentipedeFormatter
{
    use ArrayTypeCheckTrait;

    /**
     * シミュレーション結果をフロントエンド用に整形する
     * @param CentipedeSimulationResultInterface $result
     * @return array
     */
    public function format(CentipedeSimulationResultInterface $result): array
    {
        $data = array_map(function ($step) {
            return is_array($step) ? $step : $step->toArray();
        }, $result->getData());

        // CentipedeChartPointListオブジェクトから配列に変換
        $chartData = $result->getChartData()->toArray();

        return [
            'cognitive_unit_latex_text' => $result->getCognitiveUnitLatexText(),
            'cognitive_unit_value' => $result->getCognitiveUnitValue(),
            'average_of_reversed_causality' => $result->getAverageOfReversedCausality(),
            'data' => $data,
            'chart_data' => $chartData, // 変換後の配列を使用
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
     * @param array<CentipedeSimulationStepInterface|array> $data
     * @return CentipedeChartPointList
     */
    public function makeChartData(array $data): CentipedeChartPointList
    {
        // 配列の各要素がCentipedeSimulationStepInterfaceのインスタンスであることを確認
        // TODO: CentipedeSimulationStepInterfaceからなる配列のみ許容するように修正する
        foreach ($data as $index => $item) {
            if (! ($item instanceof CentipedeSimulationStepInterface) &&
                ! (is_array($item) && isset($item['t']) && isset($item['result']))) {
                $actualType = is_object($item) ? get_class($item) : gettype($item);

                throw new \InvalidArgumentException(
                    sprintf(
                        'All items in $data must be instances of %s or arrays with required keys. Item at index %d is %s.',
                        CentipedeSimulationStepInterface::class,
                        $index,
                        $actualType
                    )
                );
            }
        }

        $chartData = [];
        $lastSkippedT = 0;

        // result中にtrueが1件でもあればyは0から開始する。ない場合は1。
        $results = array_map(function ($step) {
            return is_array($step) ? $step['result'] : $step->getResult();
        }, $data);
        $yOffset = in_array(true, $results, true) ? 0 : 1;

        foreach ($data as $value) {
            // resultがtrueのデータが出た場合、それを最後にスキップしたtとして値を保存する。
            $result = is_array($value) ? $value['result'] : $value->getResult();
            $t = is_array($value) ? $value['t'] : $value->getT();

            if ($result === true) {
                $lastSkippedT = $t;
            }
            // スキップしたtが一度も出ていない間は、yはt - 1に等しい。
            if ($lastSkippedT === 0) {
                $y = $t - 1 + $yOffset;
                // スキップしたtが出た場合、スキップした点を起点(0)として、そこから1ずつインクリメントしていく。
            } else {
                $y = $t - $lastSkippedT + $yOffset;
            }
            $chartData[] = new CentipedeChartPoint($t, $y);
        }

        return new CentipedeChartPointList($chartData);
    }
}
