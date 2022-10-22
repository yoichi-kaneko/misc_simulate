<?php
namespace App\Calculations\ParticipantsSimulation;
use Faker\Generator;
use Phospr\Fraction;

class ParticipateLottery
{
    /*
     * comparisonの情報に合わせて、参加するかしないかを抽選で決定する
     */
    private $_faker;
    private $_comparison_rate;

    /**
     * Lottery constructor.
     * @param Generator $faker
     * @param array $comparison_value
     */
    public function __construct(Generator $faker, array $comparison_value)
    {
        $this->_faker = $faker;
        $this->initComparison($comparison_value);
    }

    /**
     * $comparison_value を抽選用に整形する
     * @param array $comparison_value
     */
    private function initComparison(array $comparison_value)
    {
        /*
         * $cognitive_degreeは、その [cognitive degreeの値 -> その参加率の分数表記] という配列で整形されている。
         * 分数表記を小数に変換して保存する
         */
        $this->_comparison_rate = [];

        foreach ($comparison_value as $index => $val) {
            $fraction = Fraction::fromString($val);
            $this->_comparison_rate[$index] = $fraction->toFloat();
        }
    }

    /**
     * 抽選を実行する
     * @param int $cognitive_degree
     * @return bool
     */
    public function draw(int $cognitive_degree): bool
    {
        $float_number = $this->_faker->randomFloat(10, 0, 1);
        return $this->_comparison_rate[$cognitive_degree] >= $float_number;
    }
}
