<?php

declare(strict_types=1);

namespace App\Calculations\Nash\Formatter;

use App\Calculations\Nash\DTO\NashSimulationResultInterface;
use Phospr\Fraction;

class NashFormatter
{
    private const DISPLAY_FORMAT = '[%s, %s]';

    /**
     * シミュレーション結果をフロントエンド用に整形する
     * @param NashSimulationResultInterface $result
     * @return array
     */
    public function format(NashSimulationResultInterface $result): array
    {
        // ライン状のパラメータを整形
        $line_render_params = [
            [
                'title' => 'alpha',
                'display_text' => $this->getDisplayText($result->getAlphaX(), $result->getAlphaY()),
                'x' => $result->getAlphaX()->toFloat(),
                'y' => $result->getAlphaY()->toFloat(),
            ],
            [
                'title' => 'rho beta',
                'display_text' => $this->getDisplayText($result->getRhoBetaX(), $result->getRhoBetaY()),
                'x' => $result->getRhoBetaX()->toFloat(),
                'y' => $result->getRhoBetaY()->toFloat(),
            ],
            [
                'title' => 'gamma1',
                'display_text' => $this->getDisplayText($result->getGamma1X(), null),
                'x' => $result->getGamma1X()->toFloat(),
                'y' => 0,
            ],
            [
                'title' => 'gamma2',
                'display_text' => $this->getDisplayText(null, $result->getGamma2Y()),
                'x' => 0,
                'y' => $result->getGamma2Y()->toFloat(),
            ],
            [
                'title' => 'midpoint',
                'display_text' => $this->getDisplayText($result->getMidpoint()['x'], $result->getMidpoint()['y']),
                'x' => $result->getMidpoint()['x']->toFloat(),
                'y' => $result->getMidpoint()['y']->toFloat(),
            ],
        ];
        $dot_render_params = [
            [
                'title' => 'beta',
                'display_text' => $this->getDisplayText($result->getBetaX(), $result->getBetaY()),
                'x' => $result->getBetaX()->toFloat(),
                'y' => $result->getBetaY()->toFloat(),
            ],
        ];

        // X座標でソート
        array_multisort(
            array_column($line_render_params, 'x'),
            SORT_ASC,
            $line_render_params
        );

        return [
            'report_params' => [
                'a_rho' => sprintf('%.3f', $result->getARho()->toFloat()),
            ],
            'render_params' => [
                'line' => $line_render_params,
                'dot' => $dot_render_params,
            ],
        ];
    }

    /**
     * 画面上の表示テキストを取得する
     * @param Fraction|null $x
     * @param Fraction|null $y
     * @return string
     */
    private function getDisplayText(?Fraction $x, ?Fraction $y): string
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
