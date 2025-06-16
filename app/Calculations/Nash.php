<?php

declare(strict_types=1);

namespace App\Calculations;

use App\Calculations\Nash\Simulator\NashSimulator;
use App\Calculations\Nash\Formatter\NashFormatter;

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
