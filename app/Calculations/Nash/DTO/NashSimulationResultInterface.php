<?php

declare(strict_types=1);

namespace App\Calculations\Nash\DTO;

use Phospr\Fraction;

/**
 * Nashシミュレーション結果を保持するDTOのインターフェース
 */
interface NashSimulationResultInterface
{
    /**
     * alpha_xを取得する
     * @return Fraction
     */
    public function getAlphaX(): Fraction;

    /**
     * alpha_yを取得する
     * @return Fraction
     */
    public function getAlphaY(): Fraction;

    /**
     * beta_xを取得する
     * @return Fraction
     */
    public function getBetaX(): Fraction;

    /**
     * beta_yを取得する
     * @return Fraction
     */
    public function getBetaY(): Fraction;

    /**
     * rho_beta_xを取得する
     * @return Fraction
     */
    public function getRhoBetaX(): Fraction;

    /**
     * rho_beta_yを取得する
     * @return Fraction
     */
    public function getRhoBetaY(): Fraction;

    /**
     * gamma1_xを取得する
     * @return Fraction
     */
    public function getGamma1X(): Fraction;

    /**
     * gamma2_yを取得する
     * @return Fraction
     */
    public function getGamma2Y(): Fraction;

    /**
     * midpointを取得する
     * @return array
     */
    public function getMidpoint(): array;

    /**
     * a_rhoを取得する
     * @return Fraction
     */
    public function getARho(): Fraction;
}
