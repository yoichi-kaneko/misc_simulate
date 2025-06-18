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
        $this->assertEquals(1, $result->getNumerator());
        $this->assertEquals(2, $result->getDenominator());
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

            $this->assertEquals($expected_x->getNumerator(), $result['x']->getNumerator(), "X座標の分子が期待通りではありません。");
            $this->assertEquals($expected_x->getDenominator(), $result['x']->getDenominator(), "X座標の分母が期待通りではありません。");

            $this->assertEquals($expected_y->getNumerator(), $result['y']->getNumerator(), "Y座標の分子が期待通りではありません。");
            $this->assertEquals($expected_y->getDenominator(), $result['y']->getDenominator(), "Y座標の分母が期待通りではありません。");
        }
    }

    /**
     * calcGamma1Xメソッドが正しくガンマ1のX点を計算することをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma1X()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma1X');
        $method->setAccessible(true);

        // テストケース
        $testCases = [
            // alpha_x, alpha_y, rho_beta_x, rho_beta_y, expected
            [new Fraction(2, 1), new Fraction(2, 1), new Fraction(1, 1), new Fraction(4, 1), new Fraction(6, 2)],
            [new Fraction(1, 2), new Fraction(1, 4), new Fraction(1, 4), new Fraction(1, 2), new Fraction(3, 4)],
            [new Fraction(3, 1), new Fraction(1, 1), new Fraction(2, 1), new Fraction(3, 1), new Fraction(7, 2)],
        ];

        // 各テストケースを実行
        foreach ($testCases as $index => [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y, $expected]) {
            // rho_beta_y - alpha_y が 0 になる場合はスキップ（分母が0になるため）
            if ($rho_beta_y->toFloat() == $alpha_y->toFloat()) {
                continue;
            }

            $result = $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

            // 計算結果を検証
            $this->assertEquals($expected->getNumerator(), $result->getNumerator(), "ケース $index: ガンマ1のX点の分子が期待通りではありません。");
            $this->assertEquals($expected->getDenominator(), $result->getDenominator(), "ケース $index: ガンマ1のX点の分母が期待通りではありません。");
        }
    }

    /**
     * calcGamma2Yメソッドが正しくガンマ2のY点を計算することをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcGamma2Y()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcGamma2Y');
        $method->setAccessible(true);

        // テストケース
        $testCases = [
            // alpha_x, alpha_y, rho_beta_x, rho_beta_y, expected
            [new Fraction(4, 1), new Fraction(2, 1), new Fraction(2, 1), new Fraction(3, 1), new Fraction(4, 1)],
            [new Fraction(1, 2), new Fraction(1, 3), new Fraction(1, 4), new Fraction(1, 5), new Fraction(1, 15)],
            [new Fraction(3, 1), new Fraction(0, 1), new Fraction(1, 1), new Fraction(2, 1), new Fraction(6, 2)],
        ];

        // 各テストケースを実行
        foreach ($testCases as $index => [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y, $expected]) {
            // alpha_x - rho_beta_x が 0 になる場合はスキップ（分母が0になるため）
            if ($alpha_x->toFloat() == $rho_beta_x->toFloat()) {
                continue;
            }

            $result = $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

            // 計算結果を検証
            $this->assertEquals($expected->getNumerator(), $result->getNumerator(), "ケース $index: ガンマ2のY点の分子が期待通りではありません。");
            $this->assertEquals($expected->getDenominator(), $result->getDenominator(), "ケース $index: ガンマ2のY点の分母が期待通りではありません。");
        }
    }

    /**
     * calcARhoメソッドが正しくa_rhoの値を計算することをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcARho()
    {
        // NashSimulatorクラスのインスタンスを作成
        $simulator = new NashSimulator();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($simulator);
        $method = $reflection->getMethod('calcARho');
        $method->setAccessible(true);

        // テストケース
        $testCases = [
            // alpha_x, alpha_y, rho_beta_x, rho_beta_y, expected
            [new Fraction(4, 1), new Fraction(2, 1), new Fraction(2, 1), new Fraction(3, 1), new Fraction(1, 1)],
            [new Fraction(1, 2), new Fraction(1, 3), new Fraction(1, 4), new Fraction(1, 2), new Fraction(1, 1)],
            [new Fraction(3, 1), new Fraction(1, 1), new Fraction(1, 1), new Fraction(2, 1), new Fraction(3, 4)],
        ];

        // 各テストケースを実行
        foreach ($testCases as $index => [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y, $expected]) {
            // (alpha_x - rho_beta_x) * (rho_beta_y - alpha_y) が 0 になる場合はスキップ（分母が0になるため）
            if (($alpha_x->toFloat() == $rho_beta_x->toFloat()) || ($rho_beta_y->toFloat() == $alpha_y->toFloat())) {
                continue;
            }

            $result = $method->invokeArgs($simulator, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

            // 計算結果を検証
            $this->assertEquals($expected->getNumerator(), $result->getNumerator(), "ケース $index: a_rhoの値の分子が期待通りではありません。");
            $this->assertEquals($expected->getDenominator(), $result->getDenominator(), "ケース $index: a_rhoの値の分母が期待通りではありません。");
        }
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
