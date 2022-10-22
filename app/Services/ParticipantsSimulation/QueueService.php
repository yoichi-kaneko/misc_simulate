<?php
namespace App\Services\ParticipantsSimulation;

use App\Jobs\CalculateParticipantsSimulation;
use App\Services\ProgressService;
use Illuminate\Support\Str;

class QueueService
{
    /**
     * @param int $bankers_budget
     * @param int $participation_fee
     * @param int $prize_unit
     * @param int $potential_participants
     * @param array $cognitive_degrees_distribution
     * @param string $participants_allocate_mode
     * @return string
     */
    public function setQueue(
        int $bankers_budget,
        int $participation_fee,
        int $prize_unit,
        int $potential_participants,
        array $cognitive_degrees_distribution,
        string $participants_allocate_mode
    ) : string{
        $token = Str::random();
        $_progress = new ProgressService($token);
        $_progress->init();
        CalculateParticipantsSimulation::dispatch([
            'bankers_budget' => $bankers_budget,
            'participation_fee' => $participation_fee,
            'prize_unit' => $prize_unit,
            'potential_participants' => $potential_participants,
            'cognitive_degrees_distribution' => $cognitive_degrees_distribution,
            'participants_allocate_mode' => $participants_allocate_mode,
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
