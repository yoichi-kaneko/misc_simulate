<?php

namespace App\Calculations;

use Illuminate\Support\Facades\Log;
use Phospr\Fraction;

class Nash
{
    private const DISPLAY_FORMAT = '[%s, %s]';

    /**
     * 計算を実行する
     * @param array $alpha_1
     * @param array $alpha_2
     * @param array $beta_1
     * @param array $beta_2
     * @return array
     * @throws \Exception
     */
    public function run(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2
    ): array {
        $alpha_x = Fraction::fromString($alpha_1['numerator'] . '/' . $alpha_1['denominator']);
        $alpha_y = Fraction::fromString($alpha_2['numerator'] . '/' .  $alpha_2['denominator']);
        $beta_x = Fraction::fromString($beta_1['numerator'] . '/' .  $beta_1['denominator']);
        $beta_y = Fraction::fromString($beta_2['numerator'] . '/' .  $beta_2['denominator']);
        $gamma2_y = $this->calcGamma2Y($alpha_x, $alpha_y, $beta_x, $beta_y);
        $gamma1_x = $this->calcGamma1X($alpha_x, $alpha_y, $beta_x, $beta_y);
        return [
            'render_params' => [
                [
                    'title' => 'gamma2',
                    'display_text' => sprintf(self::DISPLAY_FORMAT, '0', $gamma2_y->__toString()),
                    'x' => 0,
                    'y' => $gamma2_y->toFloat(),
                ],
                [
                    'title' => 'beta',
                    'display_text' => sprintf(self::DISPLAY_FORMAT, $beta_x->__toString(), $beta_y->__toString()),
                    'x' => $beta_x->toFloat(),
                    'y' => $beta_y->toFloat(),
                ],
                [
                    'title' => 'alpha',
                    'display_text' => sprintf(self::DISPLAY_FORMAT, $alpha_x->__toString(), $alpha_y->__toString()),
                    'x' => $alpha_x->toFloat(),
                    'y' => $alpha_y->toFloat(),
                ],
                [
                    'title' => 'gamma1',
                    'display_text' => sprintf(self::DISPLAY_FORMAT, $gamma1_x->__toString(), '0'),
                    'x' => $gamma1_x->toFloat(),
                    'y' => 0,
                ],
            ],
        ];
    }

    /**
     * ガンマ1のX点を計算する
     * @param Fraction $alpha_x
     * @param Fraction $alpha_y
     * @param Fraction $beta_x
     * @param Fraction $beta_y
     * @return Fraction
     * @throws \Exception
     */
    private function calcGamma1X(Fraction $alpha_x, Fraction $alpha_y, Fraction $beta_x, Fraction $beta_y): Fraction
    {
        $denominator = $beta_y->subtract($alpha_y);
        $numerator_1 = $alpha_x->multiply($beta_y);
        $numerator_2 = $alpha_y->multiply($beta_x);
        $numerator = $numerator_1->subtract($numerator_2);
        return $numerator->divide($denominator);
    }

    /**
     * ガンマ2のY点を計算する
     * @param Fraction $alpha_x
     * @param Fraction $alpha_y
     * @param Fraction $beta_x
     * @param Fraction $beta_y
     * @return Fraction
     * @throws \Exception
     */
    private function calcGamma2Y(Fraction $alpha_x, Fraction $alpha_y, Fraction $beta_x, Fraction $beta_y): Fraction
    {
        $denominator = $alpha_x->subtract($beta_x);
        $numerator_1 = $alpha_x->multiply($beta_y);
        $numerator_2 = $alpha_y->multiply($beta_x);
        $numerator = $numerator_1->subtract($numerator_2);
        return $numerator->divide($denominator);
    }
}