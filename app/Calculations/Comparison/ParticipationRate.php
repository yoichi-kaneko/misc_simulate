<?php
namespace App\Calculations\Comparison;

use Phospr\Fraction;

class ParticipationRate
{
    /**
     * プレイヤーの配分と効用関数による参加率配分から、最終的な参加率を計算する
     * @param array $cognitive_degrees_distribution
     * @param array $utility_function_rate
     * @return float
     */
    public static function run(array $cognitive_degrees_distribution, array $utility_function_rate)
    {
        $fraction = new Fraction(0, 1);

        // 各プレイヤー配分と効用関数から、参加率を順に加算する
        foreach ($utility_function_rate as $cognitive_degree => $display_text) {
            $distribution = (int) $cognitive_degrees_distribution[$cognitive_degree];
            // プレイヤー配分と効用関数のいずれかが0ならば加算値も0なのでスキップする
            if ($display_text == '0' || $distribution == 0) {
                continue;
            }
            $add_fraction = Fraction::fromString($display_text);

            $add_fraction = $add_fraction->multiply(new Fraction($distribution, 100));
            $fraction = $fraction->add($add_fraction);
        }

        return $fraction->toFloat();
    }
}
