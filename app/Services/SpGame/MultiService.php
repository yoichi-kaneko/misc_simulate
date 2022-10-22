<?php
namespace App\Services\SpGame;

use App\Calculations\CostFunction;
use App\Calculations\Roi;
use App\Calculations\SpGame;
use App\Services\ProgressService;
use App\Stores\TmpResultDetailStore;
use Faker\Factory;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;
use Config;

class MultiService
{
    private $is_completed = false;
    private $processing_data = [];
    private const CALCULATION_LIMIT = 20000 * 500; // 一度のrunで実行する演算回数の上限

    /**
     * マルチ計算を行う。Job経由で実行するため、結果はキャッシュに保存する
     * @param int $banker_prepared_change
     * @param int $fee
     * @param int $player
     * @param int $banker_budget_degree
     * @param int $iteration
     * @param int $initial_setup_cost
     * @param int $facility_unit_cost
     * @param int $facility_unit
     * @param int|null $random_seed
     * @param bool $get_chart_data
     * @param bool $save_each_transitions
     * @param string $token
     * @param array $processing_data
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function run(
        int $banker_prepared_change,
        int $fee,
        int $player,
        int $banker_budget_degree,
        int $iteration,
        int $initial_setup_cost,
        int $facility_unit_cost,
        int $facility_unit,
        int $random_seed = null,
        bool $get_chart_data = false,
        bool $save_each_transitions = false,
        string $token = '',
        array $processing_data = []
    ): void {
        /*
         * セントペテルスブルクゲームを$iteration回分行う。
         * このケースでは、どういう風に胴元の資金が推移するかの確認を主な目的としている
         */
        $results = $this->initResult($processing_data);
        $faker = Factory::create();
        if (!empty($random_seed)) {
            $faker->seed($random_seed + count($results['obtained_cache']));
        }
        // 各トランジションを保存する場合、以前の一時データをTruncateする
        if ($save_each_transitions) {
            $tmp_result_detail_store = new TmpResultDetailStore();
            $tmp_result_detail_store->init(count($results['obtained_cache']) == 0);
        }
        $sp_game = new SpGame($faker);

        $_progress_rate = new ProgressService($token);
        if (empty($results['obtained_cache'])) {
            $_progress_rate->set(0, $iteration, 0, true);
        }
        $processing_count = 0;

        for ($repeat = count($results['obtained_cache']); $repeat < $iteration; $repeat++) {
            $current_cache = $banker_prepared_change;
            $transitions = [];

            $transitions[] = [
                'cache' => (int) $banker_prepared_change,
                'challenge_count' => 0
            ];

            for ($player_count = 0; $player_count < $player; $player_count++) {
                $transition = $sp_game->player_try_game($fee, $current_cache, $banker_budget_degree);
                $current_cache = $transition['cache'];
                $transitions[] = $transition;
                if ($current_cache < 0) {
                    break;
                }
            }
            $results['obtained_cache'][] = $current_cache - $banker_prepared_change;
            $results['result_status'][] = $sp_game->get_result_status($banker_prepared_change, $current_cache);
            if ($save_each_transitions) {
                $tmp_result_detail_store->try_save($current_cache - $banker_prepared_change, $transitions);
            }
            $_progress_rate->set((int) ($repeat * 100 / $iteration), $iteration, $repeat);
            $processing_count++;
            // 処理件数が多くなった場合、一度処理を中断する。
            if ($this->isProcessExhausted($player_count, $processing_count)) {
                $this->suspend($results);
                if ($save_each_transitions) {
                    unset($tmp_result_detail_store);
                }
                return;
            }
        }

        if ($save_each_transitions) {
            unset($tmp_result_detail_store);
        }
        $multi_data = $this->summarize_multi_data(
            $results,
            $get_chart_data,
            $banker_prepared_change,
            $banker_budget_degree,
            CostFunction::run($initial_setup_cost, $facility_unit_cost, $facility_unit, $player),
            $save_each_transitions
        );
        $_progress_rate->setComplete($multi_data, 'multi');
        $this->is_completed = true;
    }

    /**
     * @param $processing_data
     * @return array
     */
    private function initResult($processing_data): array
    {
        if (empty($processing_data)) {
            return [
                'obtained_cache' => [],
                'result_status' => []
            ];
        }
        return $processing_data;
    }

    /**
     * @param int $player_count
     * @param int $processing_count
     * @return bool
     */
    private function isProcessExhausted(int $player_count, int $processing_count): bool
    {
        return $player_count * $processing_count > self::CALCULATION_LIMIT;
    }

    /**
     * @param array $result
     */
    private function suspend(array $result): void
    {
        $this->processing_data = $result;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    /**
     * @return array
     */
    public function getProcessingData(): array
    {
        return $this->processing_data;
    }

    /**
     * @param array $results
     * @param bool $get_chart_data
     * @param int $banker_prepared_change
     * @param int $banker_budget_degree
     * @param int $cost
     * @param bool $save_each_transitions
     * @return array
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    private function summarize_multi_data(
        array $results,
        bool $get_chart_data,
        int $banker_prepared_change,
        int $banker_budget_degree,
        int $cost,
        bool $save_each_transitions
    ): array {
        $result_status = [
            'increase' => 0,
            'decrease' => 0,
            'bankruptcy' => 0,
            'even' => 0
        ];

        foreach ($results['result_status'] as $result_status_value) {
            $result_status[$result_status_value]++;
        }
        $average = (int) array_sum($results['obtained_cache']) / count($results['obtained_cache']);
        $standard_deviation = Descriptive::standardDeviation($results['obtained_cache'], true);

        $ret = [
            'iteration' => count($results['result_status']),
            'result_status' => $result_status,
            'average' => $average,
            'standard_deviation' => $standard_deviation,
            'cost' => $cost,
            'roi' => $this->get_roi(
                $results['obtained_cache'],
                count($results['result_status']),
                $banker_prepared_change,
                $banker_budget_degree,
                $cost
            ),
            'save_each_transitions' => $save_each_transitions,
        ];

        if ($get_chart_data) {
            $ret['chart_header'] = $this->make_multi_render_header($results);
            $ret['chart_data'] = $this->make_multi_render_data($results, $ret['chart_header']);
        }

        return $ret;
    }

    /**
     * @param array $obtained_cache_array
     * @param int $iteration
     * @param int $banker_prepared_change
     * @param int $banker_budget_degree
     * @param int $cost
     * @return float
     */
    private function get_roi(
        array $obtained_cache_array,
        int $iteration,
        int $banker_prepared_change,
        int $banker_budget_degree,
        int $cost
    ): float{
        // 試行した回数分だけ、数値を加算する
        return Roi::run(
            $banker_prepared_change * $iteration,
            array_sum($obtained_cache_array) + $banker_prepared_change * $iteration,
            pow(2, $banker_budget_degree) * $iteration,
            $cost * $iteration
        );
    }

    /**
     * @param $results
     * @return array
     */
    private function make_multi_render_header($results): array
    {
        $max_cache = max($results['obtained_cache']);
        $min_cache = min($results['obtained_cache']);

        $range = $max_cache - $min_cache;

        $step = 1;

        while (!$this->is_valid_step($range, $step)) {
            $step *= 10;
        }

        return [
            'min_cache' => $min_cache,
            'max_cache' => $max_cache,
            'step' => $step
        ];
    }

    /**
     * @param $results
     * @param $chart_header
     * @return array
     */
    private function make_multi_render_data($results, $chart_header): array
    {
        $ret = [];

        $current = $chart_header['min_cache'] - ($chart_header['min_cache'] % $chart_header['step']);
        if ($chart_header['min_cache'] > 0) {
            $range_start = $chart_header['min_cache'] - ($chart_header['min_cache'] % $chart_header['step']);
        } else {
            $range_start = $chart_header['min_cache'] - $chart_header['step'] - ($chart_header['min_cache'] % $chart_header['step']);
        }
        $current = $range_start;

        while ($current <=  $chart_header['max_cache'] + $chart_header['step']) {
            $key = $this->get_key($current, $range_start, $chart_header['step']);
            $ret[$key] = [
                'x' => $current,
                'y' => 0
            ];
            $current += $chart_header['step'];
        }
        foreach ($results['obtained_cache'] as $obtained_cache) {
            $key = $this->get_key($obtained_cache, $range_start, $chart_header['step']);
            $ret[$key]['y']++;
        }

        return $ret;
    }

    /**
     * 対象の金額の値が、どのキーレンジに入るか計算する
     * @param $target_cache 対象の金額
     * @param $range_start 金額レンジの最小値
     * @param $step ステップ数
     * @return int 計算したキーレンジ
     */
    private function get_key($target_cache, $range_start, $step)
    {
        return floor(($target_cache - $range_start) / $step);
    }

    private function is_valid_step($range, $step)
    {
        $multichart_max_datasets = Config::get('render_chart.multichart_max_datasets');

        return floor($range / $step) <= $multichart_max_datasets;
    }
}
