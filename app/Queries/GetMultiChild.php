<?php
namespace App\Queries;

use App\Models\SimulateTmpResultDetail;
use App\Calculations\SpGame;
use App\Services\SpGame\Multi\ChildService;

/**
 * Class GetMultiChild
 * マルチ計算した結果のうち、指定した範囲のデータをランダムに返す
 * @package App\Queries
 */
class GetMultiChild
{
    public static function query($child_x_label, $multi_step)
    {
        $range = (new ChildService())->get_cache_range_by_label($child_x_label, $multi_step);

        $data = SimulateTmpResultDetail::whereBetween('obtained_cache', [$range['min'], $range['max']])
            ->inRandomOrder()->first();

        if (empty($data)) {
            return false;
        }
        return json_decode($data->getTransitions(), true);
    }
}
