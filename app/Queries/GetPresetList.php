<?php

namespace App\Queries;

use App\Models\SimulatePresetParam;

class GetPresetList
{
    /**
     * プリセット一覧を取得する
     * @param int $type
     * @return array
     */
    public static function query(int $type): array
    {
        return SimulatePresetParam::where('type', $type)
            ->orderBy('id')
            ->get()
            ->pluck('title', 'id')
            ->toArray();
    }
}
