<?php

declare(strict_types=1);

namespace App\Calculations;

use App\Calculations\Nash\Formatter\NashFormatter;
use App\Calculations\Nash\Simulator\NashSimulator;

class Nash
{
    private NashSimulator $simulator;
    private NashFormatter $formatter;

    public function __construct(NashSimulator $simulator = null, NashFormatter $formatter = null)
    {
        $this->simulator = $simulator;
        $this->formatter = $formatter;
    }

    /**
     * 計算を実行する
     * @param array{numerator: string, denominator: string} $alpha_1 分数形式のalpha_1パラメータ
     * @param array{numerator: string, denominator: string} $alpha_2 分数形式のalpha_2パラメータ
     * @param array{numerator: string, denominator: string} $beta_1 分数形式のbeta_1パラメータ
     * @param array{numerator: string, denominator: string} $beta_2 分数形式のbeta_2パラメータ
     * @param array{numerator: string, denominator: string} $rho 分数形式のrhoパラメータ
     * @return array{
     *     report_params: array{a_rho: string},
     *     render_params: array{
     *         line: array<array{title: string, display_text: string, x: float, y: float}>,
     *         dot: array<array{title: string, display_text: string, x: float, y: float}>
     *     }
     * } フロントエンド表示用にフォーマットされた計算結果
     * @throws \Exception シミュレーション実行中にエラーが発生した場合
     */
    public function run(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2,
        array $rho
    ): array {
        // シミュレーションを実行
        $result = $this->simulator->run(
            $alpha_1,
            $alpha_2,
            $beta_1,
            $beta_2,
            $rho
        );

        // 結果をフォーマット
        return $this->formatter->format($result);
    }
}
