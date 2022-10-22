<?php

namespace App\Calculations\ParticipantsSimulation\CognitiveDegreeAllocator;

use App\Calculations\ParticipantsSimulation\CognitiveDegreeAllocator;

class Lottery extends CognitiveDegreeAllocator
{
    /**
     * $cognitive_degree を抽選用に整形する
     * @param array $cognitive_degrees_distribution
     */
    protected function initCognitiveDegree(array $cognitive_degrees_distribution)
    {
        /*
         * $cognitive_degreeは、その [degreeの値 -> その配分] という配列で整形されている。
         * degreeの値をその配分数だけ配列に出力する事で、抽選処理を行いやすくする
         */
        $this->_cognitive_degree = [];

        foreach ($cognitive_degrees_distribution as $index => $val) {
            for($i = 0; $i < $val; $i++) {
                $this->_cognitive_degree[] = $index;
            }
        }
    }

    /**
     * 参加者のcognitive_degreeの割り当てを行う。割り当ては各々確率に応じた抽選を行う。
     * @param int $potential_participants
     * @return array
     */
    public function allocate(int $potential_participants): array
    {
        $ret = [];

        for($i = 0; $i < $potential_participants; $i++) {
            $cognitive_degree = $this->_faker->randomElement($this->_cognitive_degree);

            if (!isset($ret[$cognitive_degree])) {
                $ret[$cognitive_degree] = 1;
            } else {
                $ret[$cognitive_degree]++;
            }
        }
        ksort($ret);
        return $ret;
    }
}
