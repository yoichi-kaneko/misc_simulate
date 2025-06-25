<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Nash\Formatter;

use App\Calculations\Nash\Formatter\NashFormatter;
use Phospr\Fraction;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class NashFormatterTest extends TestCase
{
    /**
     * getDisplayTextメソッドのデータプロバイダー
     * @return array
     */
    public static function getDisplayTextDataProvider(): array
    {
        return [
            '整数の分数' => [
                'x' => new Fraction(1, 1),
                'y' => new Fraction(2, 1),
                'expected' => '[1.000, 2.000]',
            ],
            '小数になる分数' => [
                'x' => new Fraction(1, 2),
                'y' => new Fraction(1, 4),
                'expected' => '[0.500, 0.250]',
            ],
            'xがnullの場合' => [
                'x' => null,
                'y' => new Fraction(3, 1),
                'expected' => '[0, 3.000]',
            ],
            'yがnullの場合' => [
                'x' => new Fraction(5, 2),
                'y' => null,
                'expected' => '[2.500, 0]',
            ],
            '両方がnullの場合' => [
                'x' => null,
                'y' => null,
                'expected' => '[0, 0]',
            ],
        ];
    }

    /**
     * getDisplayTextメソッドが正しく表示テキストを生成することをテストします。
     * @test
     * @dataProvider getDisplayTextDataProvider
     * @return void
     */
    public function getDisplayText_入力値と変換される表示テキストの検証($x, $y, string $expected)
    {
        // NashFormatterクラスのインスタンスを作成
        $formatter = new NashFormatter();

        // Reflectionを使用してprivateメソッドにアクセス
        $reflectionMethod = new ReflectionMethod(NashFormatter::class, 'getDisplayText');
        $result = $reflectionMethod->invoke($formatter, $x, $y);

        // 結果を検証
        $this->assertSame($expected, $result);
    }

    /**
     * formatメソッドが正しくフォーマットされた結果を返すことをテストします。
     * @test
     * @return void
     */
    public function format_フォーマットされた結果を返す()
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
