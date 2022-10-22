<?php

namespace App\Services;

use App\Calculations\ParticipantsSimulation\CognitiveDegreeAllocator\Fix;
use App\Calculations\ParticipantsSimulation\CognitiveDegreeAllocator\Lottery;
use App\Calculations\ParticipantsSimulation\MeasuredExpectedParticipants;
use App\Calculations\ParticipantsSimulation\ParticipateLottery;
use Config;
use Faker\Factory;
use Faker\Generator;
use MathPHP\Statistics\Descriptive;

class ParticipantsSimulationService
{
    private const ITERATION = 10000;

    private $_cognitive_degree_allocator;
    private $_participate_lottery;

    /**
     * 参加率を計算する
     * @param array $comparison_result
     * @param array $cognitive_degrees_distribution
     * @param array $comparison_result_row
     * @param int $potential_participants
     * @param float $total_variance
     * @param string $token
     * @param string $participants_allocate_mode
     * @return void
     */
    public function run(
        array $comparison_result,
        array $cognitive_degrees_distribution,
        array $comparison_result_row,
        int $potential_participants,
        float $total_variance,
        string $participants_allocate_mode,
        string $token
    ) :void {
        $faker = Factory::create();
        $this->setAllocator($participants_allocate_mode, $faker, $cognitive_degrees_distribution);

        $this->_participate_lottery = new ParticipateLottery(
            $faker,
            $this->pluckComparisonValue($comparison_result_row)
        );

        $_progress_rate = new ProgressService($token);
        $_progress_rate->set(0, self::ITERATION, 0, true);
        $participant_number_array = $this->getParticipantNumberArray(
            $potential_participants,
            $_progress_rate
        );

        $total_variance = Descriptive::populationVariance($participant_number_array);
        $total_variance_square = sqrt($total_variance);
        $expected_participant_number = MeasuredExpectedParticipants::calculate($participant_number_array);

        $chart_header = $this->makeChartHeader($potential_participants);

        $participants_result = [
            'iteration' => self::ITERATION,
            'potential_participants' => $potential_participants,
            'expected_participants' => $expected_participant_number,
            'total_variance' => number_format($total_variance, 2),
            'total_variance_root' => (int) floor($total_variance_square),
            'first_confidence_interval' => $this->getConfidenceInterval(
                $participant_number_array,
                $expected_participant_number,
                $total_variance_square
            ),
            'second_confidence_interval' => $this->getConfidenceInterval(
                $participant_number_array,
                $expected_participant_number,
                $total_variance_square * 2
            ),
            'chart_header' => $chart_header,
            'chart_data' => $this->makeChartData($participant_number_array, $chart_header)
        ];

        $data = [
            'comparison_result' => $comparison_result,
            'participants_result' => $participants_result,
        ];
        $_progress_rate->setComplete($data, 'participants_simulation');
    }

    /**
     * @param string $participants_allocate_mode
     * @param Generator $faker
     * @param array $cognitive_degrees_distribution
     */
    private function setAllocator(
        string $participants_allocate_mode,
        Generator $faker,
        array $cognitive_degrees_distribution
    ): void {
        if ($participants_allocate_mode === 'fix') {
            $this->_cognitive_degree_allocator = new Fix(
                $faker,
                $cognitive_degrees_distribution
            );
        } else {
            $this->_cognitive_degree_allocator = new Lottery(
                $faker,
                $cognitive_degrees_distribution
            );
        }
    }

    private function pluckComparisonValue(array $comparison_result_row): array
    {
        $ret = [];

        foreach ($comparison_result_row as $key => $value) {
            $ret[$key] = $value['comparison']['display'];
        }

        return $ret;
    }

    /**
     * 参加者数をiterationで繰り返し描画する
     * @param int $potential_participants
     * @param ProgressService $_progress_rate
     * @return array
     */
    private function getParticipantNumberArray(int $potential_participants, ProgressService $_progress_rate): array
    {
        $return = [];

        for ($iteration = 0; $iteration < self::ITERATION; $iteration++) {
            $return[] = $this->drawParticipantNumber($potential_participants);
            $_progress_rate->set((int) ($iteration * 100 / self::ITERATION), self::ITERATION, $iteration);
        }
        return $return;
    }

    /**
     * 参加候補者が何人参加するか、抽選を行う
     * @param int $potential_participants
     * @return int
     */
    private function drawParticipantNumber(int $potential_participants): int
    {
        $return = 0;
        $cognitive_degree_data = $this->_cognitive_degree_allocator->allocate($potential_participants);

        foreach ($cognitive_degree_data as $cognitive_degree => $count) {
            for ($i = 0; $i < $count; $i++) {
                if ($this->_participate_lottery->draw($cognitive_degree)) {
                    $return++;
                }
            }
        }

        return $return;
    }

    /**
     * @param int $potential_participants
     * @return array
     */
    private function makeChartHeader(int $potential_participants): array
    {
        $min = 0;
        $max = $potential_participants;
        $step = 1;

        while (!$this->isValidStep($max, $step)) {
            $step *= 10;
        }
        return [
            'min' => $min,
            'max' => $potential_participants,
            'step' => $step
        ];
    }

    /**
     * @param array $participant_number_array
     * @param array $chart_header
     * @return array
     */
    private function makeChartData(array $participant_number_array, array $chart_header): array
    {
        $ret = [];
        $current = $chart_header['min'] - ($chart_header['step'] / 2);
        while ($current <=  $chart_header['max']) {
            $key = floor($current / $chart_header['step']) + 1;
            $ret[$key] = [
                'x_from' => max($current, $chart_header['min']),
                'x_to' => min($current + $chart_header['step'] - 1, $chart_header['max']),
                'x' => $current + ($chart_header['step'] / 2),
                'y' => 0
            ];
            $current += $chart_header['step'];
        }
        foreach ($participant_number_array as $participant_number) {
            foreach ($ret as $key => $values) {
                if ($participant_number >= $values['x_from'] && $participant_number <= $values['x_to']) {
                    $ret[$key]['y']++;
                    break;
                }
            }
        }

        return $ret;
    }

    /**
     * @param int $range
     * @param int $step
     * @return bool
     */
    private function isValidStep(int $range, int $step): bool
    {
        $max_datasets = Config::get('render_chart.multichart_max_datasets');
        return floor($range / $step) <= $max_datasets;
    }

    /**
     * 信頼区間内にサンプルがいくつ入るか計算する
     * @param array $samples サンプルとなる数値の集合
     * @param float $average 平均値
     * @param float $interval 求める信頼区間
     * @return int 信頼区間内に入るサンプル数
     */
    private function getConfidenceInterval(array $samples, float $average, float $interval): int
    {
        $ret = 0;
        $from = ceil($average - $interval);
        $to = floor($average + $interval);

        foreach ($samples as $sample) {
            if ($sample >= $from && $sample <= $to) {
                $ret++;
            }
        }
        return $ret;
    }
}
