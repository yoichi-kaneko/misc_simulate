<?php

namespace App\Http\Controllers;

class CentipedeController extends Controller
{
    public function index()
    {
        return view('centipede/index')
            ->with('js_file', 'js/pages/centipede.js');
    }
}
