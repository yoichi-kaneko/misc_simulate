<?php

declare(strict_types=1);

namespace App\Factories\DTO;

abstract class AbstractDTOFactory
{
    /**
     * デフォルト値でDTOを作成
     * @return mixed
     */
    abstract public function create(): mixed;

    /**
     * カスタム値でDTOを作成
     * @param array $attributes
     * @return mixed
     */
    abstract public function createWith(array $attributes): mixed;
}
