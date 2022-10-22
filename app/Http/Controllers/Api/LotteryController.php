<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateLottery;
use Phospr\Fraction;

class LotteryController extends Controller
{
    public function calculate(CalculateLottery $request): array
    {
        $post_data = $request->all();
        return [
            'result' => $this->calculateBlank($post_data)
        ];
    }

    private function calculateBlank($post_data)
    {
        $fraction = new Fraction(1, 1);
        // TODO: インターフェース確認のための仮置き。
        foreach ($post_data['lottery_rates'] as $lottery_rate) {
            $subtract_data = new Fraction((int) $lottery_rate['rate_number'], pow(10, (int) $lottery_rate['rate_digit']));
            $fraction = $fraction->subtract($subtract_data);
        }
        return $fraction->toFloat();
    }

}
