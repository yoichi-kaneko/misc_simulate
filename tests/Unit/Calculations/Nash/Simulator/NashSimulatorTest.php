<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Nash\Simulator;

use App\Calculations\Nash\Simulator\NashSimulator;
use Phospr\Fraction;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NashSimulatorTest extends TestCase
{
    /**
     * createFractionメソッドが正しく分数オブジェクトを生成することをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCreateFraction()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('createFraction');
        $method->setAccessible(true);

        // ケース1: 分母が正の整数の時、正常にFractionインスタンスを返す
        $result = $method->invokeArgs($simulator, [1, 2]);
        $this->assertInstanceOf(Fraction::class, $result);
        $this->assertSame(1, $result->getNumerator());
        $this->assertSame(2, $result->getDenominator());
    }

    /**
     * createFractionメソッドが分母が0の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCreateFractionWithZeroDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('createFraction');
        $method->setAccessible(true);

        // ケース2: 分母が0の時、例外をスローする
        $this->expectException(\Phospr\Exception\Fraction\InvalidDenominatorException::class);
        $this->expectExceptionMessage('Denominator must be an integer greater than zero');
        $method->invokeArgs($simulator, [1, 0]);
    }

    /**
     * createFractionメソッドが分母が負の整数の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCreateFractionWithNegativeDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('createFraction');
        $method->setAccessible(true);

        // ケース3: 分母が負の整数の時、例外をスローする
        $this->expectException(\Phospr\Exception\Fraction\InvalidDenominatorException::class);
        $this->expectExceptionMessage('Denominator must be an integer greater than zero');
        $method->invokeArgs($simulator, [1, -1]);
    }

    /**
     * calcMidpointメソッドが正しく中点を計算することをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcMidpoint()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcMidpoint');
        $method->setAccessible(true);

        // テストケース
        $testCases = [
            // gamma1_x, gamma2_y, expected_x, expected_y
            [new Fraction(4, 1), new Fraction(6, 1), new Fraction(2, 1), new Fraction(3, 1)],
            [new Fraction(1, 2), new Fraction(1, 4), new Fraction(1, 4), new Fraction(1, 8)],
            [new Fraction(3, 2), new Fraction(5, 3), new Fraction(3, 4), new Fraction(5, 6)],
            [new Fraction(0, 1), new Fraction(0, 1), new Fraction(0, 1), new Fraction(0, 1)],
        ];

        // 各テストケースを実行
        foreach ($testCases as [$gamma1_x, $gamma2_y, $expected_x, $expected_y]) {
            $result = $method->invokeArgs($simulator, [$gamma1_x, $gamma2_y]);

            $this->assertSame($expected_x->getNumerator(), $result['x']->getNumerator(), "X座標の分子が期待通りではありません。");
            $this->assertSame($expected_x->getDenominator(), $result['x']->getDenominator(), "X座標の分母が期待通りではありません。");

            $this->assertSame($expected_y->getNumerator(), $result['y']->getNumerator(), "Y座標の分子が期待通りではありません。");
            $this->assertSame($expected_y->getDenominator(), $result['y']->getDenominator(), "Y座標の分母が期待通りではありません。");
        }
    }

    /**
     * calcGamma1Xメソッドのデータプロバイダー
     * @return array
     */
    public static function calcGamma1XDataProvider(): array
    {
        return [
            '基本的なケース' => [
                'alpha_x' => new Fraction(2, 1),
                'alpha_y' => new Fraction(2, 1),
                'rho_beta_x' => new Fraction(1, 1),
                'rho_beta_y' => new Fraction(4, 1),
                'expected' => new Fraction(6, 2),
            ],
            '小数の分数' => [
                'alpha_x' => new Fraction(1, 2),
                'alpha_y' => new Fraction(1, 4),
                'rho_beta_x' => new Fraction(1, 4),
                'rho_beta_y' => new Fraction(1, 2),
                'expected' => new Fraction(3, 4),
            ],
            '大きな値のケース' => [
                'alpha_x' => new Fraction(3, 1),
                'alpha_y' => new Fraction(1, 1),
                'rho_beta_x' => new Fraction(2, 1),
                'rho_beta_y' => new Fraction(3, 1),
                'expected' => new Fraction(7, 2),
            ],
        ];
    }

    /**
     * calcGamma1Xメソッドが正しくガンマ1のX点を計算することをテストします。
     * @test
     * @dataProvider calcGamma1XDataProvider
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma1X(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y,
        Fraction $expected
    ) {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma1X');
        $method->setAccessible(true);

        // rho_beta_y - alpha_y が 0 になる場合はスキップ（分母が0になるため）
        if ($rho_beta_y->toFloat() == $alpha_y->toFloat()) {
            $this->markTestSkipped('分母が0になるためスキップします。');
        }

        $result = $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

        // 計算結果を検証
        $this->assertSame($expected->getNumerator(), $result->getNumerator(), "ガンマ1のX点の分子が期待通りではありません。");
        $this->assertSame($expected->getDenominator(), $result->getDenominator(), "ガンマ1のX点の分母が期待通りではありません。");
    }

    /**
     * calcGamma2Yメソッドのデータプロバイダー
     * @return array
     */
    public static function calcGamma2YDataProvider(): array
    {
        return [
            '基本的なケース' => [
                'alpha_x' => new Fraction(4, 1),
                'alpha_y' => new Fraction(2, 1),
                'rho_beta_x' => new Fraction(2, 1),
                'rho_beta_y' => new Fraction(3, 1),
                'expected' => new Fraction(4, 1),
            ],
            '小数の分数' => [
                'alpha_x' => new Fraction(1, 2),
                'alpha_y' => new Fraction(1, 3),
                'rho_beta_x' => new Fraction(1, 4),
                'rho_beta_y' => new Fraction(1, 5),
                'expected' => new Fraction(1, 15),
            ],
            'ゼロを含むケース' => [
                'alpha_x' => new Fraction(3, 1),
                'alpha_y' => new Fraction(0, 1),
                'rho_beta_x' => new Fraction(1, 1),
                'rho_beta_y' => new Fraction(2, 1),
                'expected' => new Fraction(6, 2),
            ],
        ];
    }

    /**
     * calcGamma2Yメソッドが正しくガンマ2のY点を計算することをテストします。
     * @test
     * @dataProvider calcGamma2YDataProvider
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma2Y(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y,
        Fraction $expected
    ) {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma2Y');
        $method->setAccessible(true);

        // alpha_x - rho_beta_x が 0 になる場合はスキップ（分母が0になるため）
        if ($alpha_x->toFloat() == $rho_beta_x->toFloat()) {
            $this->markTestSkipped('分母が0になるためスキップします。');
        }

        $result = $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

        // 計算結果を検証
        $this->assertSame($expected->getNumerator(), $result->getNumerator(), "ガンマ2のY点の分子が期待通りではありません。");
        $this->assertSame($expected->getDenominator(), $result->getDenominator(), "ガンマ2のY点の分母が期待通りではありません。");
    }

    /**
     * calcARhoメソッドのデータプロバイダー
     * @return array
     */
    public static function calcARhoDataProvider(): array
    {
        return [
            '基本的なケース' => [
                'alpha_x' => new Fraction(4, 1),
                'alpha_y' => new Fraction(2, 1),
                'rho_beta_x' => new Fraction(2, 1),
                'rho_beta_y' => new Fraction(3, 1),
                'expected' => new Fraction(1, 1),
            ],
            '小数の分数' => [
                'alpha_x' => new Fraction(1, 2),
                'alpha_y' => new Fraction(1, 3),
                'rho_beta_x' => new Fraction(1, 4),
                'rho_beta_y' => new Fraction(1, 2),
                'expected' => new Fraction(1, 1),
            ],
            '異なる結果値' => [
                'alpha_x' => new Fraction(3, 1),
                'alpha_y' => new Fraction(1, 1),
                'rho_beta_x' => new Fraction(1, 1),
                'rho_beta_y' => new Fraction(2, 1),
                'expected' => new Fraction(3, 4),
            ],
        ];
    }

    /**
     * calcARhoメソッドが正しくa_rhoの値を計算することをテストします。
     * @test
     * @dataProvider calcARhoDataProvider
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcARho(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y,
        Fraction $expected
    ) {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcARho');
        $method->setAccessible(true);

        // (alpha_x - rho_beta_x) * (rho_beta_y - alpha_y) が 0 になる場合はスキップ（分母が0になるため）
        if (($alpha_x->toFloat() == $rho_beta_x->toFloat()) || ($rho_beta_y->toFloat() == $alpha_y->toFloat())) {
            $this->markTestSkipped('分母が0になるためスキップします。');
        }

        $result = $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

        // 計算結果を検証
        $this->assertSame($expected->getNumerator(), $result->getNumerator(), "a_rhoの値の分子が期待通りではありません。");
        $this->assertSame($expected->getDenominator(), $result->getDenominator(), "a_rhoの値の分母が期待通りではありません。");
    }

    /**
     * calcGamma1Xメソッドが分母が0の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma1XWithZeroDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma1X');
        $method->setAccessible(true);

        // 分母が0になるケース（rho_beta_y == alpha_y）
        $alpha_x = new Fraction(2, 1);
        $alpha_y = new Fraction(3, 1);
        $rho_beta_x = new Fraction(1, 1);
        $rho_beta_y = new Fraction(3, 1); // alpha_yと同じ値

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);
    }

    /**
     * calcGamma1Xメソッドが分母が負の値の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma1XWithNegativeDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma1X');
        $method->setAccessible(true);

        // 分母が負になるケース（alpha_y > rho_beta_y）
        $alpha_x = new Fraction(2, 1);
        $alpha_y = new Fraction(4, 1);
        $rho_beta_x = new Fraction(1, 1);
        $rho_beta_y = new Fraction(3, 1); // alpha_yより小さい値

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);
    }

    /**
     * calcGamma2Yメソッドが分母が0の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma2YWithZeroDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma2Y');
        $method->setAccessible(true);

        // 分母が0になるケース（alpha_x == rho_beta_x）
        $alpha_x = new Fraction(2, 1);
        $alpha_y = new Fraction(1, 1);
        $rho_beta_x = new Fraction(2, 1); // alpha_xと同じ値
        $rho_beta_y = new Fraction(3, 1);

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);
    }

    /**
     * calcGamma2Yメソッドが分母が負の値の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma2YWithNegativeDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma2Y');
        $method->setAccessible(true);

        // 分母が負になるケース（alpha_x < rho_beta_x）
        $alpha_x = new Fraction(1, 1);
        $alpha_y = new Fraction(1, 1);
        $rho_beta_x = new Fraction(2, 1); // alpha_xより大きい値
        $rho_beta_y = new Fraction(3, 1);

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);
    }

    /**
     * calcARhoメソッドが分母が0の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcARhoWithZeroDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcARho');
        $method->setAccessible(true);

        // 分母が0になるケース（alpha_x == rho_beta_x または rho_beta_y == alpha_y）
        $alpha_x = new Fraction(2, 1);
        $alpha_y = new Fraction(3, 1);
        $rho_beta_x = new Fraction(2, 1); // alpha_xと同じ値
        $rho_beta_y = new Fraction(4, 1);

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);
    }

    /**
     * calcARhoメソッドが分母が負の値の時に例外をスローすることをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcARhoWithNegativeDenominator()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcARho');
        $method->setAccessible(true);

        // 分母が負になるケース（alpha_x < rho_beta_x または alpha_y > rho_beta_y）
        $alpha_x = new Fraction(1, 1);
        $alpha_y = new Fraction(1, 1);
        $rho_beta_x = new Fraction(2, 1); // alpha_xより大きい値
        $rho_beta_y = new Fraction(3, 1);

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);
    }
}
