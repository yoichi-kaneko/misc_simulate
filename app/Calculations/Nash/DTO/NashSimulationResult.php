<?php

declare(strict_types=1);

namespace App\Calculations\Nash\DTO;

use Phospr\Fraction;

class NashSimulationResult
{
    private Fraction $alpha_x;
    private Fraction $alpha_y;
    private Fraction $rho_beta_x;
    private Fraction $rho_beta_y;
    private Fraction $gamma1_x;
    private Fraction $gamma2_y;
    private array $midpoint;
    private Fraction $a_rho;

    public function __construct(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $rho_beta_x,
        Fraction $rho_beta_y,
        Fraction $gamma1_x,
        Fraction $gamma2_y,
        array $midpoint,
        Fraction $a_rho
    ) {
        $this->alpha_x = $alpha_x;
        $this->alpha_y = $alpha_y;
        $this->rho_beta_x = $rho_beta_x;
        $this->rho_beta_y = $rho_beta_y;
        $this->gamma1_x = $gamma1_x;
        $this->gamma2_y = $gamma2_y;
        $this->midpoint = $midpoint;
        $this->a_rho = $a_rho;
    }

    public function getAlphaX(): Fraction
    {
        return $this->alpha_x;
    }

    public function getAlphaY(): Fraction
    {
        return $this->alpha_y;
    }

    public function getRhoBetaX(): Fraction
    {
        return $this->rho_beta_x;
    }

    public function getRhoBetaY(): Fraction
    {
        return $this->rho_beta_y;
    }

    public function getGamma1X(): Fraction
    {
        return $this->gamma1_x;
    }

    public function getGamma2Y(): Fraction
    {
        return $this->gamma2_y;
    }

    public function getMidpoint(): array
    {
        return $this->midpoint;
    }

    public function getARho(): Fraction
    {
        return $this->a_rho;
    }
}