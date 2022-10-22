<?php

namespace App\Http\Controllers;

use App\Models\Simulate;
use Config;

class IndexController extends Controller
{
    public function index()
    {
        return view('index/index')
            ->with('js_file', 'js/pages/coin_tossing.js')
            ->with('params', Config::get('init_values'))
            ->with('mode_list', $this->get_mode_list())
            ->with('allocate_list', $this->get_allocate_list());
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

    /**
     * @return string[]
     */
    private function get_allocate_list(): array
    {
        return [
            'fix' => 'fix',
            'lottery' => 'lottery'
        ];
    }
}
