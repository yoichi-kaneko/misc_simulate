<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class CentipedeController extends Controller
{
    public function index()
    {
        return view('centipede/index')
            ->with('js_file', 'js/pages/centipede.js')
            ->with('case_list', $this->get_case_list());
    }

    /**
     * @return string[]
     */
    private function get_case_list(): array
    {
        return [
            '1' => 'Case1',
            '2' => 'Case2'
        ];
    }
}
