# 文字列から整数への変換方法について

## 現状の理解

現在、`NashController`から`NashSimulator`に渡される`$alpha_1`などの配列は、以下のような構造を持っています：

```php
[
    'numerator' => '1',    // 文字列型の整数
    'denominator' => '2'   // 文字列型の整数
]
```

これらの値は`CalculateNashRequest`のバリデーションにより整数値であることが保証されていますが、HTTPリクエストの性質上、文字列として送信されています。

現在の実装では、`NashSimulator`クラスの`run`メソッド内で以下のように`Fraction::fromString()`を使用して分数オブジェクトを作成しています：

```php
$alpha_x = Fraction::fromString($alpha_1['numerator'] . '/' . $alpha_1['denominator']);
```

## 推奨される実装方法

`Fraction`クラスのコンストラクタは整数型の引数を期待しているため、文字列を連結して`fromString()`メソッドを使用するよりも、直接整数に変換してコンストラクタに渡す方が適切です。

以下のように`NashSimulator`クラスの`run`メソッドを修正することを推奨します：

```php
public function run(
    array $alpha_1,
    array $alpha_2,
    array $beta_1,
    array $beta_2,
    array $rho
): NashSimulationResult {
    // 文字列から整数への明示的な変換
    $alpha_x = new Fraction((int)$alpha_1['numerator'], (int)$alpha_1['denominator']);
    $alpha_y = new Fraction((int)$alpha_2['numerator'], (int)$alpha_2['denominator']);
    $beta_x = new Fraction((int)$beta_1['numerator'], (int)$beta_1['denominator']);
    $beta_y = new Fraction((int)$beta_2['numerator'], (int)$beta_2['denominator']);
    $rho_rate = new Fraction((int)$rho['numerator'], (int)$rho['denominator']);
    
    // 以下は変更なし
    $rho_beta_x = $beta_x->multiply($rho_rate);
    $rho_beta_y = $beta_y->multiply($rho_rate);
    
    // 残りのコードは変更なし
    // ...
}
```

## この実装のメリット

1. **明示的な型変換**: 文字列から整数への変換が明示的に行われるため、コードの意図が明確になります。
2. **効率性**: 文字列連結と解析のステップを省略できるため、わずかながらパフォーマンスが向上します。
3. **エラー処理の改善**: `Fraction`コンストラクタは整数型を期待しているため、型変換を明示的に行うことでエラーの可能性を減らせます。
4. **コードの一貫性**: 値が整数であることが保証されている場合、それを直接使用する方が一貫性があります。

この修正は最小限の変更で実装でき、既存のコードの動作に影響を与えることなく、より明確で効率的なコードになります。