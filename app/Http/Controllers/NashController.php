<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class NashController extends Controller
{
    public function index()
    {
        return view('nash/index')
            ->with('js_file', 'js/pages/nash.js');
    }
}
