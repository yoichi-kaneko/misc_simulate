<?php

namespace App\Queries;


use App\Models\SimulatePresetParam;

class GetPresetDetail
{
    /**
     * @return array
     * @throws \Exception
     */
    public static function query(int $id): array
    {
        $data = SimulatePresetParam::find($id);

        if (is_null($data)) {
            Throw new \Exception('Data not found');
        }

        $data = $data->getAttributes();

        return [
            'id' => $data['id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'params' => json_decode($data['params'], true),
        ];
    }
}
