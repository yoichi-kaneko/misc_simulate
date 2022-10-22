<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateSingleSpGame;
use App\Http\Requests\CalculateMultiSpGame;
use App\Http\Requests\GetMultiChildSpGame;
use App\Models\SimulateQueue;
use App\Queries\GetMultiChild;
use App\Services\SpGame\SingleService;
use App\Services\SpGame\Multi\QueueService;

class CalculateController extends Controller
{
    public function single(CalculateSingleSpGame $request)
    {
        $post_data = $request->all();

        $data = (new SingleService())->run(
            $post_data['banker_prepared_change'],
            $post_data['participation_fee'],
            $post_data['participant_number'],
            $post_data['banker_budget_degree'],
            $post_data['initial_setup_cost'],
            $post_data['facility_unit_cost'],
            $post_data['facility_unit'],
            $post_data['random_seed']
        );
        return $data;
    }

    public function multi(CalculateMultiSpGame $request)
    {
        $post_data = $request->all();
        $queue = new QueueService();
        $token = $queue->setQueue(
            $post_data['banker_prepared_change'],
            $post_data['participation_fee'],
            $post_data['participant_number'],
            $post_data['banker_budget_degree'],
            $post_data['iteration'],
            $post_data['initial_setup_cost'],
            $post_data['facility_unit_cost'],
            $post_data['facility_unit'],
            $post_data['random_seed'],
            true,
            $post_data['save_each_transitions'] ?? false
        );
        return ['result' => 'OK', 'token' => $token];
    }

    public function multi_child(GetMultiChildSpGame $request)
    {
        $post_data = $request->all();
        $result = GetMultiChild::query($post_data['child_x_label'], $post_data['multi_step']);

        if (empty($result)) {
            return ['result' => 'ng'];
        }
        return ['result' => 'ok', 'transitions' => $result];
    }

    public function progress(string $token)
    {
        $queue = new QueueService();
        return $queue->get_progress($token);
    }
}
