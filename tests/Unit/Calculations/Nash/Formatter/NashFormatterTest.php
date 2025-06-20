<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Nash\Formatter;

use App\Calculations\Nash\Formatter\NashFormatter;
use Phospr\Fraction;
use PHPUnit\Framework\TestCase;

class NashFormatterTest extends TestCase
{
    /**
     * getDisplayTextメソッドが正しく表示テキストを生成することをテストします。
     * @test
     * @return void
     */
    public function testGetDisplayText()
    {
        // NashFormatterクラスのインスタンスを作成
        $formatter = new NashFormatter();

        // テストケース
        $testCases = [
            // x, y, expected
            [new Fraction(1, 1), new Fraction(2, 1), '[1.000, 2.000]'],
            [new Fraction(1, 2), new Fraction(1, 4), '[0.500, 0.250]'],
            [null, new Fraction(3, 1), '[0, 3.000]'],
            [new Fraction(5, 2), null, '[2.500, 0]'],
            [null, null, '[0, 0]'],
        ];

        // 各テストケースを実行
        foreach ($testCases as $index => [$x, $y, $expected]) {
            $result = $formatter->getDisplayText($x, $y);

            // 結果を検証
            $this->assertSame($expected, $result, "ケース $index: 表示テキストが期待通りではありません。");
        }
    }

    /**
     * formatメソッドが正しくフォーマットされた結果を返すことをテストします。
     * @test
     * @return void
     */
    public function testFormat()
    {
        // モックのNashSimulationResultInterfaceを作成
        $simulationResult = $this->createMock(\App\Calculations\Nash\DTO\NashSimulationResultInterface::class);

        // モックの振る舞いを設定
        $simulationResult->method('getAlphaX')->willReturn(new Fraction(1, 1));
        $simulationResult->method('getAlphaY')->willReturn(new Fraction(2, 1));
        $simulationResult->method('getBetaX')->willReturn(new Fraction(2, 1));
        $simulationResult->method('getBetaY')->willReturn(new Fraction(3, 1));
        $simulationResult->method('getRhoBetaX')->willReturn(new Fraction(3, 1));
        $simulationResult->method('getRhoBetaY')->willReturn(new Fraction(4, 1));
        $simulationResult->method('getGamma1X')->willReturn(new Fraction(5, 1));
        $simulationResult->method('getGamma2Y')->willReturn(new Fraction(6, 1));
        $simulationResult->method('getMidpoint')->willReturn([
            'x' => new Fraction(2, 1),
            'y' => new Fraction(3, 1),
        ]);
        $simulationResult->method('getARho')->willReturn(new Fraction(7, 2));

        // NashFormatterクラスのインスタンスを作成
        $formatter = new NashFormatter();

        // formatメソッドを実行
        $result = $formatter->format($simulationResult);

        // 結果を検証
        $this->assertIsArray($result);
        $this->assertArrayHasKey('report_params', $result);
        $this->assertArrayHasKey('render_params', $result);
        $this->assertArrayHasKey('a_rho', $result['report_params']);
        $this->assertSame('3.500', $result['report_params']['a_rho']);

        // render_paramsの検証
        $this->assertArrayHasKey('line', $result['render_params']);
        $this->assertArrayHasKey('dot', $result['render_params']);

        // lineの検証
        $this->assertCount(5, $result['render_params']['line']);

        // lineの各ポイントの存在を確認
        $line_titles = array_column($result['render_params']['line'], 'title');
        $this->assertContains('alpha', $line_titles);
        $this->assertContains('rho beta', $line_titles);
        $this->assertContains('gamma1', $line_titles);
        $this->assertContains('gamma2', $line_titles);
        $this->assertContains('midpoint', $line_titles);

        // dotの検証
        $this->assertCount(1, $result['render_params']['dot']);
        $this->assertSame('beta', $result['render_params']['dot'][0]['title']);

        // lineのX座標でソートされていることを確認
        $x_values = array_column($result['render_params']['line'], 'x');
        $sorted_x_values = $x_values;
        sort($sorted_x_values);
        $this->assertSame($sorted_x_values, $x_values);
    }
}
