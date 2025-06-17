<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\Simulator;

use App\Calculations\Centipede\DTO\CentipedeSimulationResult;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use App\Calculations\Centipede\Simulator\CentipedeSimulator;
use PHPUnit\Framework\TestCase;

class CentipedeSimulatorTest extends TestCase
{
    /**
     * コンストラクタが正しく動作することをテストします。
     * @test
     * @return void
     */
    public function testConstructor()
    {
        // CentipedeFormatterのモックを作成
        $formatter = $this->createMock(CentipedeFormatter::class);

        // CentipedeSimulatorのインスタンスを作成
        $simulator = new CentipedeSimulator($formatter);

        // インスタンスが正しく作成されたことを確認
        $this->assertInstanceOf(CentipedeSimulator::class, $simulator);
    }

    /**
     * calculatePatternメソッドが正しく計算結果を返すことをテストします。
     * @test
     * @return void
     */
    public function testCalculatePattern()
    {
        // テストデータ
        $baseNumerator = 3;
        $numeratorExp1 = 1;
        $numeratorExp2 = 2;
        $denominatorExp = 3;
        $maxStep = 2;

        // モックデータ
        $mockChartData = [
            ['x' => 1, 'y' => 0],
            ['x' => 2, 'y' => 1],
        ];
        $mockLatexText = '\dfrac{3^{\frac{1}{2}}}{2^{3}}';

        // CentipedeFormatterのモックを作成
        $formatter = $this->createMock(CentipedeFormatter::class);

        // makeChartDataメソッドのモック設定
        $formatter->method('makeChartData')
            ->willReturn($mockChartData);

        // makeCognitiveUnitLatexTextメソッドのモック設定
        $formatter->method('makeCognitiveUnitLatexText')
            ->with($baseNumerator, $numeratorExp1, $numeratorExp2, $denominatorExp)
            ->willReturn($mockLatexText);

        // CentipedeSimulatorのインスタンスを作成
        $simulator = new CentipedeSimulator($formatter);

        // calculatePatternメソッドを実行
        $result = $simulator->calculatePattern(
            $baseNumerator,
            $numeratorExp1,
            $numeratorExp2,
            $denominatorExp,
            $maxStep
        );

        // 結果を検証
        $this->assertInstanceOf(CentipedeSimulationResult::class, $result);
        $this->assertEquals($mockLatexText, $result->getCognitiveUnitLatexText());
        $this->assertEquals($mockChartData, $result->getChartData());

        // データの構造を検証
        $data = $result->getData();
        $this->assertCount($maxStep, $data);

        foreach ($data as $item) {
            $this->assertArrayHasKey('t', $item);
            $this->assertArrayHasKey('max_nu_value', $item);
            $this->assertArrayHasKey('left_side_value', $item);
            $this->assertArrayHasKey('right_side_value', $item);
            $this->assertArrayHasKey('result', $item);
            $this->assertIsBool($item['result']);
        }

        // Cognitive Unit値の計算が正しいことを検証
        // 期待値: pow(3, 1/2) / pow(2, 3) = 1.73205... / 8 = 0.216506...
        $expectedCognitiveUnitValue = pow($baseNumerator, ($numeratorExp1 / $numeratorExp2)) / pow(2, $denominatorExp);
        $this->assertEquals($expectedCognitiveUnitValue, $result->getCognitiveUnitValue());
    }

    /**
     * calcCognitiveUnitValueメソッドが例外をスローすることをテストします。
     * @test
     * @return void
     */
    public function testCalcCognitiveUnitValueThrowsException()
    {
        // CentipedeFormatterのモックを作成
        $formatter = $this->createMock(CentipedeFormatter::class);

        // CentipedeSimulatorのインスタンスを作成
        $simulator = new CentipedeSimulator($formatter);

        // 無効な値を設定（NaNまたは無限大になる値）
        $baseNumerator = -1;  // 負の数の平方根はNaNになる
        $numeratorExp1 = 1;
        $numeratorExp2 = 2;
        $denominatorExp = 3;
        $maxStep = 2;

        // 例外が発生することを期待
        $this->expectException(\Exception::class);

        // calculatePatternメソッドを実行
        $simulator->calculatePattern(
            $baseNumerator,
            $numeratorExp1,
            $numeratorExp2,
            $denominatorExp,
            $maxStep
        );
    }
}
