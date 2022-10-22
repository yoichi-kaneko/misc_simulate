<?php
namespace App\Calculations\Comparison;

use Phospr\Fraction;

class Row
{
    public static function run(
        int $cognitive_degree,
        int $lower_subjective_understanding,
        int $upper_subjective_understanding,
        int $lower_fee,
        int $upper_fee
    ){
        /*
         * 次のようなルールで、確率を計算する。
         *
         * subjective_understandingとfee、両方で取り得る数値のレンジに対して等確率で選択する。
         *
         * 1.subjective_understandingのみがカバーしている範囲を選択する確率に、1をかける
         * 2.subjective_understandingとfee両方がカバーしている範囲を選択する確率に、1/2をかける
         *
         * 1と2を足した確率を、ゲームへの参加確率として扱う
         *
         */

        // subjective_understandingの値がfeeより低くなった場合、0と扱う
        if ($upper_subjective_understanding <= $lower_fee) {
            return [
                'value' => 0,
                'display' => '0',
            ];
        }
        // subjective_understandingの値がfeeより高くなった場合、1と扱う
        if ($lower_subjective_understanding > $upper_fee) {
            return [
                'value' => 1,
                'display' => '1',
            ];
        }
        $denominator = max($upper_subjective_understanding, $upper_fee)
            - min ($lower_subjective_understanding, $lower_fee);

        $common_numerator = min($upper_subjective_understanding, $upper_fee)
            - max($lower_subjective_understanding, $lower_fee);

        $fraction = new Fraction($common_numerator,$denominator * 2);
        $subjective_numerator = $upper_subjective_understanding - $upper_fee;

        if ($subjective_numerator > 0) {
            $subjective_fraction = new Fraction($subjective_numerator, $denominator);
            $fraction = $fraction->add($subjective_fraction);
        }

        return [
            'value' => $fraction->toFloat(),
            'display' => self::parseFraction($cognitive_degree, $fraction->getNumerator(), $fraction->getDenominator())
        ];
    }

    private static function parseFraction(int $cognitive_degree, int $numerator, int $denominator)
    {
        if ($numerator == 0) {
            return '0';
        }
        if ($numerator == $denominator) {
            return '1';
        }

        // 本来想定される分母と違って約分されていた場合、可能ならばその分母になるように値をかける
        $new_denominator = $cognitive_degree * 2;
        if ($denominator != $new_denominator && $new_denominator % $denominator == 0) {
            $numerator = $numerator * ($new_denominator / $denominator);
            $denominator = $new_denominator;
        }

        return sprintf('%d/%d', $numerator, $denominator);
    }
}
