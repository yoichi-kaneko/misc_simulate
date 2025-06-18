<?php

declare(strict_types=1);

namespace App\Factories\DTO\Centipede;

use App\Calculations\Centipede\DTO\CentipedeSimulationStep;
use App\Factories\DTO\AbstractDTOFactory;

/**
 * CentipedeSimulationStepを作成するファクトリ
 */
class CentipedeSimulationStepFactory extends AbstractDTOFactory
{
    /**
     * デフォルト値でCentipedeSimulationStepを作成
     * @return CentipedeSimulationStep
     */
    public function create(): CentipedeSimulationStep
    {
        return new CentipedeSimulationStep(
            1,                // t
            0,                // maxNuValue
            '',               // leftSideValue
            '',               // rightSideValue
            false             // result
        );
    }

    /**
     * カスタム値でCentipedeSimulationStepを作成
     * @param array $attributes
     * @return CentipedeSimulationStep
     */
    public function createWith(array $attributes): CentipedeSimulationStep
    {
        $defaults = [
            't' => 1,
            'maxNuValue' => 0,
            'leftSideValue' => '',
            'rightSideValue' => '',
            'result' => false,
        ];

        $attributes = array_merge($defaults, $attributes);

        return new CentipedeSimulationStep(
            $attributes['t'],
            $attributes['maxNuValue'],
            $attributes['leftSideValue'],
            $attributes['rightSideValue'],
            $attributes['result']
        );
    }

    /**
     * 配列からCentipedeSimulationStepオブジェクトを作成
     * @param array $data
     * @return CentipedeSimulationStep
     */
    public function createFromArray(array $data): CentipedeSimulationStep
    {
        // 最低限必要なキーがあるか確認
        if (!isset($data['t']) || !isset($data['result'])) {
            throw new \InvalidArgumentException('Array must contain at least "t" and "result" keys');
        }

        return $this->createWith([
            't' => $data['t'],
            'maxNuValue' => $data['max_nu_value'] ?? 0,
            'leftSideValue' => $data['left_side_value'] ?? '',
            'rightSideValue' => $data['right_side_value'] ?? '',
            'result' => $data['result'],
        ]);
    }

    /**
     * 配列からCentipedeSimulationStepオブジェクトの配列を作成
     * @param array $dataArray
     * @return array
     */
    public function createManyFromArray(array $dataArray): array
    {
        return array_map([$this, 'createFromArray'], $dataArray);
    }
}