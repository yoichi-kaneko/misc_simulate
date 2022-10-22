<?php

namespace App\Calculations\ParticipantsSimulation\CognitiveDegreeAllocator;

use App\Calculations\ParticipantsSimulation\CognitiveDegreeAllocator;
use Faker\Generator;
use MathPHP\Algebra;

class Fix extends CognitiveDegreeAllocator
{
    private $_non_fraction_data;

    // 参加者は20人ごとに区切って割り当てを行う。端数が発生したら別途抽選処理を行う
    private const PARTICIPANTS_UNIT_AMOUNT = 20;
    // cognitive_degreeの配分の合計値。これは必ず合計100になる。他の値ではエラーチェックに引っかかる。
    private const COGNITIVE_DEGREE_DISTRIBUTION_TOTAL = 100;

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
            // この配列を使うのは20人ごとに区切って端数が発生した場合のみであり、合計20要素になるよう配列を整形する
            $amount = $val * (self::PARTICIPANTS_UNIT_AMOUNT / self::COGNITIVE_DEGREE_DISTRIBUTION_TOTAL);
            for($i = 0; $i < $amount; $i++) {
                $this->_cognitive_degree[] = $index;
            }
        }
    }

    /**
     * 参加者のcognitive_degreeの割り当てを行う。割り当ては各々 degreeに合わせて分配する
     * @param int $potential_participants
     * @return array
     */
    public function allocate(int $potential_participants): array
    {
        // 最大公約数でも割り切れない端数を記録する。端数がある場合、これに対してランダム性のある抽選を行う
        $fraction = $potential_participants % self::PARTICIPANTS_UNIT_AMOUNT;

        // 割り切れる値に対して、値を計算してセットする。
        $ret = $this->getNonFractionData($potential_participants - $fraction);

        // 参加者に端数がない場合はここで処理を終了、ある場合には端数の割り当てを行う
        if ($fraction == 0) {
            return $ret;
        }

        $cognitive_degree_array = $this->_cognitive_degree;
        shuffle($cognitive_degree_array);
        for ($i = 0; $i < $fraction; $i++) {
            $cognitive_degree = current($cognitive_degree_array);

            if (!isset($ret[$cognitive_degree])) {
                $ret[$cognitive_degree] = 1;
            } else {
                $ret[$cognitive_degree]++;
            }
            next($cognitive_degree_array);
        }
        ksort($ret);
        return $ret;
    }

    private function getNonFractionData(int $non_fraction_amount): array
    {
        if (isset($this->_non_fraction_data[$non_fraction_amount])) {
            return $this->_non_fraction_data[$non_fraction_amount];
        }
        $data = [];
        foreach ($this->_cognitive_degrees_distribution as $index => $val) {
            $data[$index] = (int) ($non_fraction_amount * $val / self::COGNITIVE_DEGREE_DISTRIBUTION_TOTAL);
        }
        $this->_non_fraction_data[$non_fraction_amount] = $data;
        return $this->_non_fraction_data[$non_fraction_amount];
    }
}
