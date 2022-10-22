<?php
namespace App\Calculations\Comparison;

use Phospr\Fraction;

class TotalVariance
{
    /**
     * プレイヤーの配分と効用関数による参加率配分から、最終的な分散率を計算する
     * @param int $potential_participants
     * @param array $cognitive_degrees_distribution
     * @param array $utility_function_rate
     * @return float
     */
    public static function run(
        int $potential_participants,
        array $cognitive_degrees_distribution,
        array $utility_function_rate
    ): float {
        $fraction = new Fraction(0, 1);

        // 各プレイヤー配分と効用関数から、参加率を順に加算する
        foreach ($utility_function_rate as $cognitive_degree => $display_text) {
            $distribution = (int) $cognitive_degrees_distribution[$cognitive_degree];
            // プレイヤー配分と効用関数のいずれかが0ならば加算値も0なのでスキップする
            if ($display_text == '0' || $distribution == 0) {
                continue;
            }

            $add_fraction = Fraction::fromString($display_text);
            $add_fraction = $add_fraction->multiply(self::get_second_term($add_fraction));
            $add_fraction = $add_fraction->multiply(new Fraction($distribution, 100));
            // 演算する項目数が多くなると分母、分子が大きくなるので、都度下5桁まで丸める
            $value = round($add_fraction->toFloat(), 5);
            $add_fraction = new Fraction((int) ($value * 100000), 100000);
            $fraction = $fraction->add($add_fraction);
        }

        return $potential_participants * $fraction->toFloat();
    }

    /**
     * 第二項を計算して返す
     * @param Fraction $fraction
     * @return Fraction
     */
    private static function get_second_term(Fraction $fraction): Fraction
    {
        /*
         * 第二項は、0から1の小数nに対して、1-nである。
         */
        $numerator = $fraction->getNumerator();
        $denominator = $fraction->getDenominator();
        return new Fraction($denominator - $numerator, $denominator);
    }
}
