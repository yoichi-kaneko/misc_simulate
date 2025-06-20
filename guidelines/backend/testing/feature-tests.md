# フィーチャーテストのガイドライン

## JSONレスポンステスト

JSONレスポンスを返すAPIエンドポイントをテストする際には、レスポンスの構造と内容の両方を検証することが重要です。JSONレスポンスの徹底的なテストを確保するために、以下のガイドラインに従うべきです。

### JSON構造のアサーション

#### 指定されたキーを持つ連想配列の場合

指定されたキーを持つ連想配列をテストする場合、配列の内部構造をチェックします：

```php
$response->assertJsonStructure([
    'result',
    'data' => [
        'user' => [
            'id',
            'name',
            'email',
            // 予想されるすべてのキーを含める
        ],
    ],
]);
```

#### 非連想配列の場合

非連想配列（インデックス配列）の場合、構造全体をチェックすると冗長で保守が難しくなります。代わりに：

1. **配列の最初の要素の構造をチェックする**：

```php
// data.itemsの最初の要素の構造をチェック
$this->assertArrayHasKey('id', $response->json('data.items.0'));
$this->assertArrayHasKey('name', $response->json('data.items.0'));
$this->assertArrayHasKey('value', $response->json('data.items.0'));
```

2. **配列の要素数をチェックする**：

```php
// data.itemsの要素数をチェック
$this->assertCount(10, $response->json('data.items'));
```

### 実装例

以下は、これらのガイドラインに従ったJSONレスポンスのテスト方法の完全な例です：

```php
public function test_api_endpoint()
{
    $response = $this->getJson('/api/endpoint');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'result',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'items', // キーが存在することだけをチェック
            ],
        ]);

    // data.itemsの最初の要素の構造をチェック
    $this->assertArrayHasKey('id', $response->json('data.items.0'));
    $this->assertArrayHasKey('name', $response->json('data.items.0'));
    $this->assertArrayHasKey('value', $response->json('data.items.0'));

    // data.itemsの要素数をチェック
    $this->assertCount(10, $response->json('data.items'));
}
```

### メリット

このアプローチには以下のようないくつかのメリットがあります：

1. **保守性**：配列に新しい要素が追加されても更新が必要ないため、テストの保守性が高まります。
2. **可読性**：本質的な構造に焦点を当てているため、テストの可読性が向上します。
3. **パフォーマンス**：大きなテストデータ構造を作成する必要がないため、テストの効率が向上します。
4. **信頼性**：構造と要素数の両方をチェックするため、テストの信頼性が高まります。

## 関連ガイドライン

- [ユニットテストのガイドライン](unit-tests.md)
- [テストカバレッジのガイドライン](coverage.md)
