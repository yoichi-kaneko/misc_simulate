<?php
namespace App\Services\SpGame\Multi;

use App\Jobs\CalculateMulti;
use App\Services\ProgressService;
use Illuminate\Support\Str;

class QueueService
{
    /**
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
     * @return string
     */
    public function setQueue(
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
        bool $save_each_transitions = false
    ) : string{
        $token = Str::random();
        $_progress = new ProgressService($token);
        $_progress->init();
        CalculateMulti::dispatch([
            'banker_prepared_change' => $banker_prepared_change,
            'participation_fee' => $fee,
            'participant_number' => $player,
            'banker_budget_degree' => $banker_budget_degree,
            'iteration' => $iteration,
            'random_seed' =>$random_seed,
            'initial_setup_cost' =>$initial_setup_cost,
            'facility_unit_cost' =>$facility_unit_cost,
            'facility_unit' =>$facility_unit,
            'get_chart_data' => $get_chart_data,
            'save_each_transitions' => $save_each_transitions,
            'token' => $token
        ]);
        return $token;
    }

    public  function get_progress(string $token): array
    {
        $_progress = new ProgressService($token);
        return json_decode($_progress->get(), true);
    }
}
