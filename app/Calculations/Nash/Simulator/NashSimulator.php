<?php

declare(strict_types=1);

namespace App\Calculations\Nash\Simulator;

use App\Calculations\Nash\DTO\NashSimulationResult;
use App\Calculations\Nash\DTO\NashSimulationResultInterface;
use Phospr\Fraction;

class NashSimulator
{
    /**
     * シミュレーションを実行する
     * @param array{numerator: string, denominator: string} $alpha_1
     * @param array{numerator: string, denominator: string} $alpha_2
     * @param array{numerator: string, denominator: string} $beta_1
     * @param array{numerator: string, denominator: string} $beta_2
     * @param array{numerator: string, denominator: string} $rho
     * @return NashSimulationResultInterface
     * @throws \Exception
     */
    public function run(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2,
        array $rho
    ): NashSimulationResultInterface {
        // HTTPリクエストからの値のためstring型で受け取っている。中身は整数であることは前処理で保証されている。
        $alpha_x = $this->createFraction((int) $alpha_1['numerator'], (int) $alpha_1['denominator']);
        $alpha_y = $this->createFraction((int) $alpha_2['numerator'], (int) $alpha_2['denominator']);
        $beta_x = $this->createFraction((int) $beta_1['numerator'], (int) $beta_1['denominator']);
        $beta_y = $this->createFraction((int) $beta_2['numerator'], (int) $beta_2['denominator']);
        $rho_rate = $this->createFraction((int) $rho['numerator'], (int) $rho['denominator']);
        $rho_beta_x = $beta_x->multiply($rho_rate);
        $rho_beta_y = $beta_y->multiply($rho_rate);

        if ($rho_beta_y->toFloat() <= $alpha_y->toFloat()) {
            throw new \Exception('ガンマの値が求められませんでした。');
        }

        $gamma2_y = $this->calcGamma2Y($alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y);
        $gamma1_x = $this->calcGamma1X($alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y);
        $midpoint = $this->calcMidpoint($gamma1_x, $gamma2_y);
        $a_rho = $this->calcARho($alpha_x, $alpha_y, $rho_beta_x, $rho_beta_y);

        return new NashSimulationResult(
            $alpha_x,
            $alpha_y,
            $beta_x,
            $beta_y,
            $rho_beta_x,
            $rho_beta_y,
            $gamma1_x,
            $gamma2_y,
            $midpoint,
            $a_rho
        );
    }

    /**
     * 分数オブジェクトを生成する
     * @param int $numerator 分子
     * @param int $denominator 分母
     * @return Fraction
     */
    private function createFraction(int $numerator, int $denominator): Fraction
    {
        return new Fraction($numerator, $denominator);
    }

    /**
     * ガンマ1のX点を計算する
     * @param Fraction $alpha_x
     * @param Fraction $alpha_y
     * @param Fraction $rho_beta_x
     * @param Fraction $rho_beta_y
     * @return Fraction
     * @throws \Exception
     */
    private function calcGamma1X(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y
    ): Fraction {
        $denominator = $rho_beta_y->subtract($alpha_y);
        $numerator_1 = $alpha_x->multiply($rho_beta_y);
        $numerator_2 = $alpha_y->multiply($rho_beta_x);
        $numerator = $numerator_1->subtract($numerator_2);

        return $numerator->divide($denominator);
    }

    /**
     * ガンマ2のY点を計算する
     * @param Fraction $alpha_x
     * @param Fraction $alpha_y
     * @param Fraction $rho_beta_x
     * @param Fraction $rho_beta_y
     * @return Fraction
     * @throws \Exception
     */
    private function calcGamma2Y(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y
    ): Fraction {
        $denominator = $alpha_x->subtract($rho_beta_x);
        $numerator_1 = $alpha_x->multiply($rho_beta_y);
        $numerator_2 = $alpha_y->multiply($rho_beta_x);
        $numerator = $numerator_1->subtract($numerator_2);

        return $numerator->divide($denominator);
    }

    /**
     * 中点を計算する
     * @param Fraction $gamma1_x
     * @param Fraction $gamma2_y
     * @return array{
     *     x: Fraction,
     *     y: Fraction,
     * }
     */
    private function calcMidpoint(Fraction $gamma1_x, Fraction $gamma2_y): array
    {
        $mid_x = $gamma1_x->divide($this->createFraction(2, 1));
        $mid_y = $gamma2_y->divide($this->createFraction(2, 1));

        return [
            'x' => $mid_x,
            'y' => $mid_y,
        ];
    }

    /**
     * a_rhoの値を計算する
     * @param Fraction $alpha_x
     * @param Fraction $alpha_y
     * @param Fraction $rho_beta_x
     * @param Fraction $rho_beta_y
     * @return Fraction
     */
    private function calcARho(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y
    ): Fraction {
        $denominator_1 = $alpha_x->subtract($rho_beta_x);
        $denominator_2 = $rho_beta_y->subtract($alpha_y);
        $denominator = $denominator_1->multiply($denominator_2);

        $numerator_1 = $alpha_x->multiply($rho_beta_y);
        $numerator_2 = $alpha_y->multiply($rho_beta_x);
        $numerator_3 = $rho_beta_x->multiply($rho_beta_y)->multiply($this->createFraction(2, 1));
        $numerator = $numerator_1->add($numerator_2)->subtract($numerator_3);

        return $numerator->divide($denominator)->divide($this->createFraction(2, 1));
    }
}
