<?php

namespace App\Http\Controllers;

use App\Models\SimulatePresetParam;
use App\Queries\GetPresetList;
use Config;

class NonLinearController extends Controller
{
    public function index()
    {
        return view('non-linear/index')
            ->with('js_file', 'js/pages/non_linear_coin_tossing.js')
            ->with('params', Config::get('init_values'))
            ->with('preset_list', $this->getPresetList())
            ->with('mode_list', $this->getModeList())
            ->with('allocate_list', $this->getAllocateList());
    }

    /**
     * プリセット一覧を取得する
     * @return array
     */
    private function getPresetList(): array
    {
        return ['0' => '(Select Preset)'] + GetPresetList::query(SimulatePresetParam::TYPE_NONLINEAR_COINTOSSING);
    }

    /**
     * @return string[]
     */
    private function getModeList(): array
    {
        return [
            'multi' => 'multi',
            'single' => 'single'
        ];
    }

    /**
     * @return string[]
     */
    private function getAllocateList(): array
    {
        return [
            'fix' => 'fix',
            'lottery' => 'lottery'
        ];
    }
}
