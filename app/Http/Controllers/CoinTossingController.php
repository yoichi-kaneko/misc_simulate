<?php

namespace App\Http\Controllers;
use App\Models\Simulate;
use Config;

class CoinTossingController extends Controller
{
    public function index()
    {
        return view('coin-tossing/index')
            ->with('js_file', 'js/pages/coin_tossing.js')
            ->with('params', Config::get('init_values'))
            ->with('mode_list', $this->get_mode_list());
    }

    /**
     * @return string[]
     */
    private function get_mode_list(): array
    {
        return [
            'multi' => 'multi',
            'single' => 'single'
        ];
    }
}
