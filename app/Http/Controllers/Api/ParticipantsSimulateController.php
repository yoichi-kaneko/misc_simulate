<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateParticipantsSimulate;
use App\Services\ParticipantsSimulation\QueueService;

class ParticipantsSimulateController extends Controller
{
    public function calculate(CalculateParticipantsSimulate $request): array
    {
        $post_data = $request->all();
        $queue = new QueueService();
        $token = $queue->setQueue(
            $post_data['bankers_budget'],
            $post_data['participation_fee'],
            $post_data['prize_unit'],
            $post_data['potential_participants'],
            $post_data['cognitive_degrees_distribution'],
            $post_data['participants_allocate_mode']
        );
        return ['result' => 'OK', 'token' => $token];
    }

}
