<?php

namespace App\Calculations\ParticipantsSimulation;

/**
 * 参加者の期待値の実測を返す
 */
class MeasuredExpectedParticipants
{

    /**
     * @param $participant_number_array
     * @return float
     */
    public static function calculate($participant_number_array): float
    {
        $result = array_sum($participant_number_array) / count($participant_number_array);
        return number_format($result, 2);
    }
}
