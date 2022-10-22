<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeletePreset;
use App\Http\Requests\SavePreset;
use App\Queries\GetPresetDetail;
use App\Queries\GetPresetList;
use App\Stores\PresetStore;

class PresetController extends Controller
{
    /**
     * プリセットを保存する
     * @param SavePreset $request
     * @return string[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function save(SavePreset $request)
    {
        $params = $request->toArray();
        $store = app()->make(PresetStore::class);
        $store->save($params);

        return [
            'result' => 'ok',
            'preset_list' => $this->getPresetList($params['type']),
        ];
    }

    /**
     * プリセットを保存する
     * @param DeletePreset $request
     * @return string[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function delete(DeletePreset $request)
    {
        $params = $request->toArray();
        $store = app()->make(PresetStore::class);
        $store->delete($params['id']);

        return [
            'result' => 'ok',
            'preset_list' => $this->getPresetList($params['type']),
        ];
    }

    /**
     * プリセットリストを取得する
     * @param int $type
     * @return array
     */
    private function getPresetList(int $type): array
    {
        return ['0' => '(Select Preset)'] + GetPresetList::query($type);
    }

    /**
     * プリセットの結果を取得する
     * @param int $id
     * @return array
     */
    public function find(int $id)
    {
        return GetPresetDetail::query($id);
    }

}
