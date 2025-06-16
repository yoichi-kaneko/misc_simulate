<?php

declare(strict_types=1);

namespace App\Factories\DTO\Nash;

use App\Calculations\Nash\DTO\NashSimulationResult;
use App\Factories\DTO\AbstractDTOFactory;
use Phospr\Fraction;

class NashSimulationResultFactory extends AbstractDTOFactory
{
    /**
     * デフォルト値でNashSimulationResultを作成
     * @return NashSimulationResult
     */
    public function create(): NashSimulationResult
    {
        return new NashSimulationResult(
            new Fraction(1, 2),  // alpha_x
            new Fraction(2, 3),  // alpha_y
            new Fraction(3, 4),  // beta_x
            new Fraction(4, 5),  // beta_y
            new Fraction(5, 6),  // rho_beta_x
            new Fraction(6, 7),  // rho_beta_y
            new Fraction(7, 8),  // gamma1_x
            new Fraction(8, 9),  // gamma2_y
            [                    // midpoint
                'x' => new Fraction(9, 10),
                'y' => new Fraction(10, 11)
            ],
            new Fraction(11, 12) // a_rho
        );
    }

    /**
     * カスタム値でNashSimulationResultを作成
     * @param array $attributes
     * @return NashSimulationResult
     */
    public function createWith(array $attributes): NashSimulationResult
    {
        $defaults = [
            'alpha_x' => new Fraction(1, 2),
            'alpha_y' => new Fraction(2, 3),
            'beta_x' => new Fraction(3, 4),
            'beta_y' => new Fraction(4, 5),
            'rho_beta_x' => new Fraction(5, 6),
            'rho_beta_y' => new Fraction(6, 7),
            'gamma1_x' => new Fraction(7, 8),
            'gamma2_y' => new Fraction(8, 9),
            'midpoint' => [
                'x' => new Fraction(9, 10),
                'y' => new Fraction(10, 11)
            ],
            'a_rho' => new Fraction(11, 12)
        ];

        $attributes = array_merge($defaults, $attributes);

        if (
            !isset($attributes['midpoint']['x']) ||
            !isset($attributes['midpoint']['y']) ||
            $attributes['midpoint']['x'] instanceof Fraction === false ||
            $attributes['midpoint']['y'] instanceof Fraction === false
        ) {
            throw new \InvalidArgumentException('Midpoint must contain both x and y values.');
        }

        return new NashSimulationResult(
            $attributes['alpha_x'],
            $attributes['alpha_y'],
            $attributes['beta_x'],
            $attributes['beta_y'],
            $attributes['rho_beta_x'],
            $attributes['rho_beta_y'],
            $attributes['gamma1_x'],
            $attributes['gamma2_y'],
            $attributes['midpoint'],
            $attributes['a_rho']
        );
    }

    /**
     * 分数値を簡単に作成するヘルパーメソッド
     * @param int $numerator
     * @param int $denominator
     * @return Fraction
     */
    public function fraction(int $numerator, int $denominator): Fraction
    {
        return new Fraction($numerator, $denominator);
    }
}
