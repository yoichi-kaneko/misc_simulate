<?php
namespace App\Services\SpGame;

use App\Calculations\CostFunction;
use App\Calculations\Roi;
use Faker\Factory;
use App\Calculations\SpGame;

class SingleService
{

    /**
     * @param int $banker_prepared_change
     * @param int $fee
     * @param int $player
     * @param int $banker_budget_degree
     * @param int $initial_setup_cost
     * @param int $facility_unit_cost
     * @param int $facility_unit
     * @param int|null $random_seed
     * @return array
     */
    public function run(
        int $banker_prepared_change,
        int $fee,
        int $player,
        int $banker_budget_degree,
        int $initial_setup_cost,
        int $facility_unit_cost,
        int $facility_unit,
        int $random_seed = null
    ): array {
        /*
         * セントペテルスブルクゲームを1回行う。
         * このケースでは、どういう風に胴元の資金が推移するかの確認を主な目的としている
         */
        $faker = Factory::create();
        if (!empty($random_seed)) {
            $faker->seed($random_seed);
        }
        $sp_game = new SpGame($faker);

        $transitions[] = [
            'cache' => (int) $banker_prepared_change,
            'challenge_count' => 0
        ];
        $current_cache = $banker_prepared_change;
        for ($player_count = 0; $player_count < $player; $player_count++) {
            $transition = $sp_game->player_try_game($fee, $current_cache, $banker_budget_degree);
            $transitions[] = $transition;
            $current_cache = $transition['cache'];
            if ($current_cache < 0) {
                break;
            }
        }
        $cost = CostFunction::run($initial_setup_cost, $facility_unit_cost, $facility_unit, $player);

        return [
            'start' => $banker_prepared_change,
            'end' => $current_cache,
            'tried_players' => $player_count,
            'result' => $sp_game->get_result_status($banker_prepared_change, $current_cache),
            'cost' => $cost,
            'roi' => Roi::run($banker_prepared_change, $current_cache, pow(2, $banker_budget_degree), $cost),
            'transitions' => $transitions
        ];
    }
}
