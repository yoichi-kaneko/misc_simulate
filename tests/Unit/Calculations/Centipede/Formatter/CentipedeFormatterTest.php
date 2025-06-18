<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\Formatter;

use App\Calculations\Centipede\DTO\CentipedeSimulationResultInterface;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use PHPUnit\Framework\TestCase;

class CentipedeFormatterTest extends TestCase
{
    /**
     * formatメソッドが正しくフォーマットされた結果を返すことをテストします。
     * @test
     * @return void
     */
    public function testFormat()
    {
        // モックのCentipedeSimulationResultInterfaceを作成
        $simulationResult = $this->createMock(CentipedeSimulationResultInterface::class);

        // モックの振る舞いを設定
        $simulationResult->method('getCognitiveUnitLatexText')->willReturn('\dfrac{3^{\frac{1}{2}}}{2^{3}}');
        $simulationResult->method('getCognitiveUnitValue')->willReturn(0.375);
        $simulationResult->method('getAverageOfReversedCausality')->willReturn(0.5);
        $simulationResult->method('getData')->willReturn([
            ['t' => 1, 'result' => false],
            ['t' => 2, 'result' => true],
        ]);
        $simulationResult->method('getChartData')->willReturn([
            ['x' => 1, 'y' => 1],
            ['x' => 2, 'y' => 0],
        ]);

        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        // formatメソッドを実行
        $result = $formatter->format($simulationResult);

        // 結果を検証
        $this->assertIsArray($result);
        $this->assertArrayHasKey('cognitive_unit_latex_text', $result);
        $this->assertArrayHasKey('cognitive_unit_value', $result);
        $this->assertArrayHasKey('average_of_reversed_causality', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('chart_data', $result);

        $this->assertEquals('\dfrac{3^{\frac{1}{2}}}{2^{3}}', $result['cognitive_unit_latex_text']);
        $this->assertEquals(0.375, $result['cognitive_unit_value']);
        $this->assertEquals(0.5, $result['average_of_reversed_causality']);
        $this->assertEquals([
            ['t' => 1, 'result' => false],
            ['t' => 2, 'result' => true],
        ], $result['data']);
        $this->assertEquals([
            ['x' => 1, 'y' => 1],
            ['x' => 2, 'y' => 0],
        ], $result['chart_data']);
    }

    /**
     * makeCognitiveUnitLatexTextメソッドが正しくLatexテキストを生成することをテストします。
     * @test
     * @return void
     */
    public function testMakeCognitiveUnitLatexText()
    {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        // テストケース
        $testCases = [
            // baseNumerator, numeratorExp1, numeratorExp2, denominatorExp, expected
            [3, 1, 2, 3, '\dfrac{3^{\frac{1}{2}}}{2^{3}}'],
            [5, 2, 3, 4, '\dfrac{5^{\frac{2}{3}}}{2^{4}}'],
            [7, 3, 4, 5, '\dfrac{7^{\frac{3}{4}}}{2^{5}}'],
        ];

        // 各テストケースを実行
        foreach ($testCases as $index => [$baseNumerator, $numeratorExp1, $numeratorExp2, $denominatorExp, $expected]) {
            $result = $formatter->makeCognitiveUnitLatexText(
                $baseNumerator,
                $numeratorExp1,
                $numeratorExp2,
                $denominatorExp
            );

            // 結果を検証
            $this->assertEquals($expected, $result, "ケース $index: Latexテキストが期待通りではありません。");
        }
    }

    /**
     * makeChartDataメソッドが正しくチャートデータを生成することをテストします。
     * @test
     * @return void
     */
    public function testMakeChartData()
    {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        // テストケース1: resultがすべてfalseの場合
        $data1 = [
            ['t' => 1, 'result' => false],
            ['t' => 2, 'result' => false],
            ['t' => 3, 'result' => false],
        ];
        $expected1 = [
            ['x' => 1, 'y' => 1],
            ['x' => 2, 'y' => 2],
            ['x' => 3, 'y' => 3],
        ];
        $result1 = $formatter->makeChartData($data1);
        $this->assertEquals($expected1, $result1, "ケース1: チャートデータが期待通りではありません。");

        // テストケース2: resultにtrueが含まれる場合
        $data2 = [
            ['t' => 1, 'result' => false],
            ['t' => 2, 'result' => true],
            ['t' => 3, 'result' => false],
        ];
        $expected2 = [
            ['x' => 1, 'y' => 0],
            ['x' => 2, 'y' => 0],
            ['x' => 3, 'y' => 1],
        ];
        $result2 = $formatter->makeChartData($data2);
        $this->assertEquals($expected2, $result2, "ケース2: チャートデータが期待通りではありません。");

        // テストケース3: 複数のtrueが含まれる場合
        $data3 = [
            ['t' => 1, 'result' => false],
            ['t' => 2, 'result' => true],
            ['t' => 3, 'result' => false],
            ['t' => 4, 'result' => true],
            ['t' => 5, 'result' => false],
        ];
        $expected3 = [
            ['x' => 1, 'y' => 0],
            ['x' => 2, 'y' => 0],
            ['x' => 3, 'y' => 1],
            ['x' => 4, 'y' => 0],
            ['x' => 5, 'y' => 1],
        ];
        $result3 = $formatter->makeChartData($data3);
        $this->assertEquals($expected3, $result3, "ケース3: チャートデータが期待通りではありません。");
    }
}
