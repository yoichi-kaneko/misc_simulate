<?php

namespace App\Calculations;

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
        return [
            'render_params' => [
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
            ],
        ];
    }
}