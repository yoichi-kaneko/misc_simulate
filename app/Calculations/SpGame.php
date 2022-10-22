<?php

namespace App\Calculations;
use Faker\Generator;

class SpGame
{
    /*
     * セントペテルスブルクゲームを実行するシミュレーションクラス。
     * このゲームは次のルールにより実施される。
     *
     * 1. 胴元1人と、複数のプレイヤーからなるゲームである
     * 2. 胴元は初期の資金としてキャッシュを保持している
     * 3. プレイヤーは参加費を支払いゲームに参加し、結果に応じて払い戻しを受ける（個別のゲームルールは後述）
     * 4. これにより胴元は資金に対して参加費を足して払い戻しを引く計算を行う
     * 5. これを参加したプレイヤー分繰り返し、胴元の資金の変化を観察する
     *
     * プレイヤーの個別のゲームの払い戻し額は次のルールで決める。
     *
     * 1. 払い戻し額を1セントから開始する
     * 2. プレイヤーはコインコスを行う。（確率的に2分の1のチェック）
     * 3. コイントスに負けた場合、現在の払い戻し額を受け取りそのプレイヤーのゲームは終了する
     * 4. コイントスに買った場合、払い戻し額を2倍にして、再度のコイントスを行う
     * 5. 次のコイントスで負ければ現在の払い戻し額を受け取る。勝てば再度コイントスを行う。以下、これを繰り返す
     *
     * このように、コイントスに負けるまでコイントスを繰り返し、勝った回数分の2の乗数が払い戻される。
     * ただし、「最大挑戦回数」が設定されており、その回数までコイントスに勝った場合、そこでその分の金額が払い戻される。
     * （最大挑戦回数が10なら、10回目で終了し、9回勝利した分の払い戻しとなる）
     *
     * また、いずれのプレイヤーのゲームにより胴元の資金がマイナスになった場合、
     * それ以上の支払いが困難（破産状態）であるとしてそこでゲーム全体を終了し、残ったプレイヤーのゲームも行わない。
     */

    private $_faker;

    /**
     * SpGame constructor.
     * @param Generator $faker
     */
    public function __construct(Generator $faker)
    {
        $this->_faker = $faker;
    }

    /**
     * ゲーム開始時と終了時の資金額から、ゲームの結果を返す
     * @param int $start_cache
     * @param int $end_cache
     * @return string 結果に応じた文字列
     */
    public function get_result_status(int $start_cache, int $end_cache): string
    {
        // 資金がマイナスである場合、破産と見なす
        if ($end_cache < 0) {
            return 'bankruptcy';
        }
        // 資金が増加した場合、増加と見なす
        if ($start_cache < $end_cache) {
            return 'increase';
        }
        if ($start_cache == $end_cache) {
            return 'even';
        }
        if ($start_cache > $end_cache) {
            return 'decrease';
        }
    }

    /**
     * @param int $fee
     * @param int $cache
     * @param int $banker_budget_degree
     * @return array
     */
    public function player_try_game(int $fee, int $cache, int $banker_budget_degree): array
    {
        $payback = 2;
        $count = 1;
        while ($count < $banker_budget_degree) {
            // 上限回数の1つ手前の挑戦の時は、ゲーム終了とする
            if ($count == $banker_budget_degree) {
                // $payback = $payback * 2;
                break;
            }
            $match_result = $this->_faker->randomElement([true, false]);
            if (!$match_result) {
                break;
            }
            $payback = $payback * 2;
            $count++;
        }

        return [
            'cache' => $cache + $fee - $payback,
            'challenge_count' => $count
        ];
    }
}
