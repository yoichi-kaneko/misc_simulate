<?php

declare(strict_types=1);

namespace App\Traits;

trait ArrayTypeCheckTrait
{
    /**
     * 配列の要素が全て指定されたクラスまたはインターフェースのインスタンスであることを確認
     *
     * @param array $array チェック対象の配列
     * @param string $type 期待されるクラスまたはインターフェース名
     * @param string $paramName パラメータ名（エラーメッセージ用）
     * @throws \InvalidArgumentException 型が一致しない場合
     * @return void
     */
    protected function assertArrayOfType(array $array, string $type, string $paramName = 'array'): void
    {
        foreach ($array as $index => $item) {
            if (! $item instanceof $type) {
                $actualType = is_object($item) ? get_class($item) : gettype($item);

                throw new \InvalidArgumentException(
                    sprintf(
                        'All items in $%s must be instances of %s. Item at index %d is %s.',
                        $paramName,
                        $type,
                        $index,
                        $actualType
                    )
                );
            }
        }
    }
}
