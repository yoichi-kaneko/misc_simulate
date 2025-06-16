<?php

declare(strict_types=1);

namespace App\Calculations;

use App\Calculations\Centipede\CentipedeDataCombiner;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use App\Calculations\Centipede\Simulator\CentipedeSimulator;

/**
 * Centipede計算を行うシミュレーター
 */
class Centipede
{
    private CentipedeSimulator $simulator;
    private CentipedeFormatter $formatter;
    private CentipedeDataCombiner $dataCombiner;

    /**
     * コンストラクタ
     * @param CentipedeSimulator $simulator
     * @param CentipedeFormatter $formatter
     * @param CentipedeDataCombiner $dataCombiner
     */
    public function __construct(
        CentipedeSimulator $simulator,
        CentipedeFormatter $formatter,
        CentipedeDataCombiner $dataCombiner
    ) {
        $this->simulator = $simulator;
        $this->formatter = $formatter;
        $this->dataCombiner = $dataCombiner;
    }

    /**
     * 計算を実行する
     * @param array $patterns
     * @param int $max_step
     * @param int|null $max_rc
     * @param array|null $combination_player_1
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException|\Exception
     */
    public function run(
        array $patterns,
        int $max_step,
        ?int $max_rc,
        ?array $combination_player_1
    ): array {
        $pattern_data = [];

        foreach ($patterns as $key => $pattern_val) {
            $simulationResult = $this->simulator->calculatePattern(
                (int) $pattern_val['base_numerator'],
                (int) $pattern_val['numerator_exp_1'],
                (int) $pattern_val['numerator_exp_2'],
                (int) $pattern_val['denominator_exp'],
                $max_step
            );
            $pattern_data[$key] = $this->formatter->format($simulationResult);
        }

        if (!is_null($combination_player_1)) {
            $combination_data = $this->dataCombiner->combine($combination_player_1, $pattern_data);
        } else {
            $combination_data = null;
        }

        return [
            'result' => 'ok',
            'render_params' => [
                'max_step' => $max_step,
                'max_rc' => $max_rc,
            ],
            'pattern_data' => $pattern_data,
            'combination_data' => $combination_data,
        ];
    }
}
