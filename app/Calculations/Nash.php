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
     * @param array $rho
     * @return array
     * @throws \Exception
     */
    public function run(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2,
        array $rho
    ): array {
        $alpha_x = Fraction::fromString($alpha_1['numerator'] . '/' . $alpha_1['denominator']);
        $alpha_y = Fraction::fromString($alpha_2['numerator'] . '/' .  $alpha_2['denominator']);
        $beta_x = Fraction::fromString($beta_1['numerator'] . '/' .  $beta_1['denominator']);
        $beta_y = Fraction::fromString($beta_2['numerator'] . '/' .  $beta_2['denominator']);
        $rho_rate = Fraction::fromString($rho['numerator'] . '/' . $rho['denominator']);
        $multipied_beta_x = $beta_x->multiply($rho_rate);
        $multipied_beta_y = $beta_y->multiply($rho_rate);

        if ($multipied_beta_y->toFloat() <= $alpha_y->toFloat()) {
            throw new \Exception('ガンマの値が求められませんでした。');
        }

        $gamma2_y = $this->calcGamma2Y($alpha_x, $alpha_y, $multipied_beta_x, $multipied_beta_y);
        $gamma1_x = $this->calcGamma1X($alpha_x, $alpha_y, $multipied_beta_x, $multipied_beta_y);
        $midpoint = $this->calcMidpoint($gamma1_x, $gamma2_y);

        $render_params = [
            [
                'title' => 'alpha',
                'display_text' => $this->getDisplayText($alpha_x, $alpha_y),
                'x' => $alpha_x->toFloat(),
                'y' => $alpha_y->toFloat(),
            ],
            [
                'title' => 'beta',
                'display_text' => $this->getDisplayText($multipied_beta_x, $multipied_beta_y),
                'x' => $multipied_beta_x->toFloat(),
                'y' => $multipied_beta_y->toFloat(),
            ],
            [
                'title' => 'gamma1',
                'display_text' => $this->getDisplayText($gamma1_x, null),
                'x' => $gamma1_x->toFloat(),
                'y' => 0,
            ],
            [
                'title' => 'gamma2',
                'display_text' => $this->getDisplayText(null, $gamma2_y),
                'x' => 0,
                'y' => $gamma2_y->toFloat(),
            ],
            [
                'title' => 'midpoint',
                'display_text' => $this->getDisplayText($midpoint['x'], $midpoint['y']),
                'x' => $midpoint['x']->toFloat(),
                'y' => $midpoint['y']->toFloat(),
            ],
        ];

        // X座標でソート
        array_multisort(
            array_column($render_params, 'x'),
            SORT_ASC,
            $render_params
        );

        return [
            'render_params' => $render_params,
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
    private function calcGamma1X(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $beta_x,
        Fraction $beta_y
    ): Fraction {
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
    private function calcGamma2Y(
        Fraction $alpha_x,
        Fraction $alpha_y,
        Fraction $beta_x,
        Fraction $beta_y
    ): Fraction {
        $denominator = $alpha_x->subtract($beta_x);
        $numerator_1 = $alpha_x->multiply($beta_y);
        $numerator_2 = $alpha_y->multiply($beta_x);
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
        $mid_x = $gamma1_x->divide(new Fraction(2, 1));
        $mid_y = $gamma2_y->divide(new Fraction(2, 1));

        return [
            'x' => $mid_x,
            'y' => $mid_y,
        ];
    }

    /**
     * 画面上の表示テキストを取得する
     * @param Fraction|null $x
     * @param Fraction|null $y
     * @return string
     */
    public function getDisplayText(?Fraction $x, ?Fraction $y): string
    {
        if (is_null($x)) {
            $x_text = '0';
        } else {
            $x_text = sprintf('%.3f', $x->toFloat());
        }
        if (is_null($y)) {
            $y_text = '0';
        } else {
            $y_text = sprintf('%.3f', $y->toFloat());
        }

        return sprintf(self::DISPLAY_FORMAT, $x_text, $y_text);
    }
}
