<?php
namespace App\Services\SpGame\Multi;

class ChildService
{
    /**
     * 指定したラベルとステップ数を元に、そのラベルで表示している金額の具体的な範囲を算出する
     * @param $label
     * @param $step
     * @return array minとmaxの結果の配列
     */
    public function get_cache_range_by_label($label, $step): array
    {
        return [
            'min' => (int) $label,
            'max' => (int) $label + $step - 1,
        ];
    }
}
