<?php

namespace Tests\Traits;

trait ArrayObjectCheckTrait
{
    /**
     * 配列内にオブジェクトが含まれていないことを確認
     *
     * @param array $array チェック対象の配列
     * @param string $message カスタムエラーメッセージ（オプション）
     * @throws \PHPUnit\Framework\ExpectationFailedException オブジェクトが見つかった場合
     * @return void
     */
    protected function assertArrayHasNoObjects(array $array, string $message = ''): void
    {
        $objects = $this->findObjectsInArray($array);

        if (!empty($objects)) {
            $defaultMessage = sprintf(
                'Array contains %d object(s). First object found: %s at path: %s',
                count($objects),
                get_class($objects[0]['object']),
                $objects[0]['path']
            );

            $this->fail($message ?: $defaultMessage);
        }

        $this->assertTrue(true); // アサーションカウントを増やすため
    }

    /**
     * 配列内のすべてのオブジェクトを再帰的に検索
     *
     * @param array $array 検索対象の配列
     * @param string $path 現在のパス（内部使用）
     * @return array 見つかったオブジェクトの情報
     */
    private function findObjectsInArray(array $array, string $path = 'root'): array
    {
        $objects = [];

        foreach ($array as $key => $value) {
            $currentPath = $path . '.' . $key;

            if (is_object($value)) {
                $objects[] = [
                    'object' => $value,
                    'path' => $currentPath,
                ];
            } elseif (is_array($value)) {
                $nestedObjects = $this->findObjectsInArray($value, $currentPath);
                $objects = array_merge($objects, $nestedObjects);
            }
        }

        return $objects;
    }
}
