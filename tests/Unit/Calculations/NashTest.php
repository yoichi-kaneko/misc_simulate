<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations;

use App\Calculations\Nash;
use PHPUnit\Framework\TestCase;
use Phospr\Fraction;
use ReflectionClass;

class NashTest extends TestCase
{
    /**
     * calcMidpointメソッドが正しく中点を計算することをテストします。
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function testCalcMidpoint()
    {
        // Nashクラスのインスタンスを作成
        $nash = new Nash();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($nash);
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
            $result = $method->invokeArgs($nash, [$gamma1_x, $gamma2_y]);

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
        // Nashクラスのインスタンスを作成
        $nash = new Nash();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($nash);
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

            $result = $method->invokeArgs($nash, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

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
        // Nashクラスのインスタンスを作成
        $nash = new Nash();

        // privateメソッドにアクセスするためのReflectionを設定
        $reflection = new ReflectionClass($nash);
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

            $result = $method->invokeArgs($nash, [$alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y]);

            // 計算結果を検証
            $this->assertEquals($expected->getNumerator(), $result->getNumerator(), "ケース $index: ガンマ2のY点の分子が期待通りではありません。");
            $this->assertEquals($expected->getDenominator(), $result->getDenominator(), "ケース $index: ガンマ2のY点の分母が期待通りではありません。");
        }
    }
}
