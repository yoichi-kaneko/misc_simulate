<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede;

use App\Calculations\Centipede\CentipedeDataCombiner;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use PHPUnit\Framework\TestCase;

class CentipedeDataCombinerTest extends TestCase
{
    /**
     * combineメソッドが正常に動作することをテストします。
     * @test
     * @return void
     */
    public function testCombineWorksCorrectly()
    {
        // CentipedeFormatterのモックを作成
        $formatterMock = $this->createMock(CentipedeFormatter::class);

        // makeChartDataメソッドのモックを設定
        $formatterMock->method('makeChartData')
            ->willReturn([
                ['x' => 1, 'y' => 0],
                ['x' => 2, 'y' => 1],
            ]);

        // CentipedeDataCombinerのインスタンスを作成
        $combiner = new CentipedeDataCombiner($formatterMock);

        // テスト用の入力データを作成
        $combinationPlayer1 = [
            'pattern1' => '1', // Player1が1を選択
        ];

        $patternData = [
            'pattern1_1' => [
                'data' => [
                    ['t' => 1, 'result' => false],
                    ['t' => 2, 'result' => true],
                ],
                'cognitive_unit_latex_text' => '\dfrac{3^{\frac{1}{2}}}{2^{3}}',
                'cognitive_unit_value' => 0.375,
            ],
            'pattern1_2' => [
                'data' => [
                    ['t' => 1, 'result' => true],
                    ['t' => 2, 'result' => false],
                ],
                'cognitive_unit_latex_text' => '\dfrac{5^{\frac{2}{3}}}{2^{4}}',
                'cognitive_unit_value' => 0.5,
            ],
        ];

        // combineメソッドを実行
        $result = $combiner->combine($combinationPlayer1, $patternData);

        // 結果を検証
        $this->assertIsArray($result);
        $this->assertArrayHasKey('pattern1', $result);

        $patternResult = $result['pattern1'];

        // データ構造の検証
        $this->assertArrayHasKey('data', $patternResult);
        $this->assertArrayHasKey('chart_data', $patternResult);
        $this->assertArrayHasKey('cognitive_unit_latex_text_1', $patternResult);
        $this->assertArrayHasKey('cognitive_unit_latex_text_2', $patternResult);
        $this->assertArrayHasKey('cognitive_unit_value_1', $patternResult);
        $this->assertArrayHasKey('cognitive_unit_value_2', $patternResult);
        $this->assertArrayHasKey('average_of_reversed_causality', $patternResult);

        // 値の検証
        // Player1が1を選択した場合、偶数インデックス(0)はpatternData1から、奇数インデックス(1)はpatternData2から取得される
        $expectedData = [
            ['t' => 1, 'result' => false], // インデックス0: patternData1から
            ['t' => 2, 'result' => false], // インデックス1: patternData2から
        ];

        $this->assertEquals($expectedData, $patternResult['data']);
        $this->assertEquals([['x' => 1, 'y' => 0], ['x' => 2, 'y' => 1]], $patternResult['chart_data']);
        $this->assertEquals('\dfrac{3^{\frac{1}{2}}}{2^{3}}', $patternResult['cognitive_unit_latex_text_1']);
        $this->assertEquals('\dfrac{5^{\frac{2}{3}}}{2^{4}}', $patternResult['cognitive_unit_latex_text_2']);
        $this->assertEquals(0.375, $patternResult['cognitive_unit_value_1']);
        $this->assertEquals(0.5, $patternResult['cognitive_unit_value_2']);
        $this->assertEquals(0.5, $patternResult['average_of_reversed_causality']);
    }
}
