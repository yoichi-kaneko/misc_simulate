<?php

namespace App\Stores;

use App\Models\SimulatePresetParam;

/**
 * Class PresetStore
 * @package App\Stores
 */
class PresetStore
{
    /**
     * @var SimulatePresetParam
     */
    private $simulate_preset_param;

    public function __construct(SimulatePresetParam $simulate_preset_param)
    {
        $this->simulate_preset_param = $simulate_preset_param;
    }

    /**
     * パラメータを保存する
     * @param array $params
     * @return bool
     */
    public function save(array $params): bool
    {
        $save_data = [
            'type' => $params['type'],
            'title' => $params['title'],
            'params' => json_encode($params['params']),
        ];
        if (!isset($params['id'])) {
            return $this->simulate_preset_param->insert($save_data);
        } else {
            return $this->simulate_preset_param->where('id', $params['id'])
                ->update($save_data);
        }
    }

    /**
     * レコードを削除する
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        return $this->simulate_preset_param->where('id', $id)->delete();
    }
}
