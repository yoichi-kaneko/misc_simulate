<?php

declare(strict_types=1);

namespace App\Calculations\Nash\DTO;

use Phospr\Fraction;

final class NashSimulationResult implements NashSimulationResultInterface
{
    private readonly Fraction $alpha_x;
    private readonly Fraction $alpha_y;
    private readonly Fraction $beta_x;
    private readonly Fraction $beta_y;
    private readonly Fraction $rho_beta_x;
    private readonly Fraction $rho_beta_y;
    private readonly Fraction $gamma1_x;
    private readonly Fraction $gamma2_y;
    private readonly array $midpoint;
    private readonly Fraction $a_rho;

    /**
     * @param Fraction $alpha_x
     * @param Fraction $alpha_y
     * @param Fraction $beta_x
     * @param Fraction $beta_y
     * @param Fraction $rho_beta_x
     * @param Fraction $rho_beta_y
     * @param Fraction $gamma1_x
     * @param Fraction $gamma2_y
     * @param array{
     *     x: Fraction,
     *     y: Fraction,
     * } $midpoint
     * @param Fraction $a_rho
     */
    public function __construct(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $beta_x,
        Fraction $beta_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y,
        Fraction $gamma1_x,
        Fraction $gamma2_y,
        array $midpoint,
        Fraction $a_rho
    ) {
        $this->alpha_x = $alpha_x;
        $this->alpha_y = $alpha_y;
        $this->beta_x = $beta_x;
        $this->beta_y = $beta_y;
        $this->rho_beta_x = $rho_beta_x;
        $this->rho_beta_y = $rho_beta_y;
        $this->gamma1_x = $gamma1_x;
        $this->gamma2_y = $gamma2_y;
        $this->midpoint = $midpoint;
        $this->a_rho = $a_rho;
    }

    /**
     * alpha_xを取得する
     * @return Fraction
     */
    public function getAlphaX(): Fraction
    {
        return $this->alpha_x;
    }

    /**
     * alpha_yを取得する
     * @return Fraction
     */
    public function getAlphaY(): Fraction
    {
        return $this->alpha_y;
    }

    /**
     * beta_xを取得する
     * @return Fraction
     */
    public function getBetaX(): Fraction
    {
        return $this->beta_x;
    }

    /**
     * beta_yを取得する
     * @return Fraction
     */
    public function getBetaY(): Fraction
    {
        return $this->beta_y;
    }

    /**
     * rho_beta_xを取得する
     * @return Fraction
     */
    public function getRhoBetaX(): Fraction
    {
        return $this->rho_beta_x;
    }

    /**
     * rho_beta_yを取得する
     * @return Fraction
     */
    public function getRhoBetaY(): Fraction
    {
        return $this->rho_beta_y;
    }

    /**
     * gamma1_xを取得する
     * @return Fraction
     */
    public function getGamma1X(): Fraction
    {
        return $this->gamma1_x;
    }

    /**
     * gamma2_yを取得する
     * @return Fraction
     */
    public function getGamma2Y(): Fraction
    {
        return $this->gamma2_y;
    }

    /**
     * midpointを取得する
     * @return array
     */
    public function getMidpoint(): array
    {
        return $this->midpoint;
    }

    /**
     * a_rhoを取得する
     * @return Fraction
     */
    public function getARho(): Fraction
    {
        return $this->a_rho;
    }
}
