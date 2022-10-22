<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Simulate;

class SettingController extends Controller
{
    public function delete($id)
    {
        $data = Simulate::find($id);

        if(empty($data)) {
            abort('500');
        }
        $data->delete();
        return ['result' => 'ok'];
    }

}
