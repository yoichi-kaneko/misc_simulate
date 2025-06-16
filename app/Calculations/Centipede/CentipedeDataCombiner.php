<?php

declare(strict_types=1);

namespace App\Calculations\Centipede;

use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use Illuminate\Support\Arr;

/**
 * Centipedeシミュレーション結果を結合するクラス
 */
class CentipedeDataCombiner
{
    private CentipedeFormatter $formatter;

    /**
     * コンストラクタ
     * @param CentipedeFormatter $formatter
     */
    public function __construct(CentipedeFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * 2つのシミュレート結果を合算する
     * @param array $combinationPlayer1
     * @param array $patternData
     * @return array
     */
    public function combine(array $combinationPlayer1, array $patternData): array
    {
        $combinationData = [];

        foreach ($combinationPlayer1 as $combinationPlayerKey => $combinationPlayerVal) {
            // Requestのバリデーションで $combinationPlayerVal には1,2いずれかがセットされていると想定
            $patternData1 = $patternData[$combinationPlayerKey . '_1']['data'];
            $patternData2 = $patternData[$combinationPlayerKey . '_2']['data'];
            $player1Is1 = ($combinationPlayerVal === '1');

            $data = [];
            $maxCount = count($patternData1);
            for ($i = 0; $i < $maxCount; $i++) {
                // Player1が1で$iが偶数（0から始まるため）、Player1が2で$iが奇数の場合にAをセット
                if (
                    $player1Is1 && $i % 2 === 0 ||
                    !$player1Is1 && $i % 2 > 0
                ) {
                    $data[] = $patternData1[$i];
                } else {
                    $data[] = $patternData2[$i];
                }
            }
            $chartData = $this->formatter->makeChartData($data);

            $averageOfReversedCausality = (array_sum(Arr::pluck($chartData, 'y')) / count($chartData));

            $combinationData[$combinationPlayerKey] = [
                'data' => $data,
                'chart_data' => $chartData,
                'cognitive_unit_latex_text_1' => $patternData[$combinationPlayerKey . '_1']['cognitive_unit_latex_text'],
                'cognitive_unit_latex_text_2' => $patternData[$combinationPlayerKey . '_2']['cognitive_unit_latex_text'],
                'cognitive_unit_value_1' => $patternData[$combinationPlayerKey . '_1']['cognitive_unit_value'],
                'cognitive_unit_value_2' => $patternData[$combinationPlayerKey . '_2']['cognitive_unit_value'],
                'average_of_reversed_causality' => $averageOfReversedCausality,
            ];
        }

        return $combinationData;
    }
}