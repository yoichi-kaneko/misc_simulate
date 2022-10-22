<?php

namespace App\Http\Controllers;
use Config;

class LotteryController extends Controller
{
    public function index()
    {
        return view('lottery/index')
            ->with('js_file', 'js/pages/lottery.js')
            ->with('params', Config::get('init_values'));
    }
}
