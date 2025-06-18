<?php

declare(strict_types=1);

namespace App\Calculations\Centipede;

use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use App\Traits\ArrayTypeCheckTrait;
use Illuminate\Support\Arr;

/**
 * Centipedeシミュレーション結果を結合するクラス
 */
class CentipedeDataCombiner
{
    use ArrayTypeCheckTrait;
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
     * @param array $patternData 各キーに'data'要素としてCentipedeSimulationStepInterfaceの配列を含む配列
     * @return array
     */
    public function combine(array $combinationPlayer1, array $patternData): array
    {
        $combinationData = [];

        foreach ($combinationPlayer1 as $combinationPlayerKey => $combinationPlayerVal) {
            // Requestのバリデーションで $combinationPlayerVal には1,2いずれかがセットされていると想定
            $patternData1 = $patternData[$combinationPlayerKey . '_1']['data'];
            $patternData2 = $patternData[$combinationPlayerKey . '_2']['data'];

            // 配列の要素が全てCentipedeSimulationStepInterfaceのインスタンスであることを確認
            // TODO: 型の問題が解消されたらここで $this->assertArrayOfType によるチェックを行う

            $player1Is1 = ($combinationPlayerVal === '1');

            $data = [];
            // patternData1とpatternData2の長さが一致していることを確認
            if (count($patternData1) !== count($patternData2)) {
                throw new \InvalidArgumentException('patternData1 と patternData2 の長さが一致していません');
            }

            $maxCount = count($patternData1);
            for ($i = 0; $i < $maxCount; $i++) {
                // Player1が1で$iが偶数（0から始まるため）、Player1が2で$iが奇数の場合にAをセット
                if (
                    $player1Is1 && $i % 2 === 0 ||
                    ! $player1Is1 && $i % 2 > 0
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
