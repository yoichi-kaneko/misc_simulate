# PHP開発ガイドライン

## App\Calculations クラス

App\Calculations ディレクトリには、ゲーム理論の計算を行うクラスが含まれています。現在、以下のクラスが実装されています：

### Nash 関連クラス

Nash 関連クラスは、ナッシュの社会厚生の計算を行います。計算処理とフォーマット処理を分離するために、以下のクラス構成になっています：

#### Nash クラス

Nash クラスは、計算とフォーマットの橋渡しを行うファサードクラスです。主な特徴：

- `App\Calculations\Nash\Simulator\NashSimulator` と `App\Calculations\Nash\Formatter\NashFormatter` を内部で使用
- 主要メソッド：
  - `run()`: 計算のエントリーポイント（シミュレーションの実行とフォーマットを委譲）

#### NashSimulator クラス

NashSimulator クラス (`App\Calculations\Nash\Simulator\NashSimulator`) は、ナッシュの社会厚生の計算ロジックを担当します。主な特徴：

- 分数計算に `Phospr\Fraction` ライブラリを使用
- 主要メソッド：
  - `run()`: シミュレーションを実行し、`App\Calculations\Nash\DTO\NashSimulationResult` を返す
  - `calcGamma1X()`: ガンマ1のX座標を計算
  - `calcGamma2Y()`: ガンマ2のY座標を計算
  - `calcMidpoint()`: 中点を計算
  - `calcARho()`: a_rho値を計算

#### NashFormatter クラス

NashFormatter クラス (`App\Calculations\Nash\Formatter\NashFormatter`) は、計算結果をフロントエンド用に整形します。主な特徴：

- 主要メソッド：
  - `format()`: `App\Calculations\Nash\DTO\NashSimulationResult` をフロントエンド用の配列に変換
  - `getDisplayText()`: 表示用テキストを生成

#### NashSimulationResult クラス

NashSimulationResult クラス (`App\Calculations\Nash\DTO\NashSimulationResult`) は、シミュレーション結果を保持するDTOです。主な特徴：

- シミュレーション結果のすべての値を保持
- ゲッターメソッドを提供
- 各ゲッターメソッドには適切なPHPDocを記載（プロパティの説明と戻り値の型）

### Centipede 関連クラス

Centipede 関連クラスは、ムカデゲームのシミュレーションを行います。計算処理とフォーマット処理を分離するために、以下のクラス構成になっています：

#### Centipede クラス

Centipede クラスは、計算とフォーマットの橋渡しを行うファサードクラスです。主な特徴：

- `App\Calculations\Centipede\Simulator\CentipedeSimulator`、`App\Calculations\Centipede\Formatter\CentipedeFormatter`、`App\Calculations\Centipede\CentipedeDataCombiner` を内部で使用
- 主要メソッド：
  - `run()`: 計算のエントリーポイント（シミュレーションの実行、結合処理、フォーマットを委譲）

#### CentipedeSimulator クラス

CentipedeSimulator クラス (`App\Calculations\Centipede\Simulator\CentipedeSimulator`) は、ムカデゲームのシミュレーション計算ロジックを担当します。主な特徴：

- 複雑な数学的計算を `eval()` を使用して実行
- 主要メソッド：
  - `calculatePattern()`: 特定パターンの計算を実行し、`App\Calculations\Centipede\DTO\CentipedeSimulationResult` を返す
  - `calcCognitiveUnitValue()`: 認知単位の値を計算

#### CentipedeFormatter クラス

CentipedeFormatter クラス (`App\Calculations\Centipede\Formatter\CentipedeFormatter`) は、計算結果をフロントエンド用に整形します。主な特徴：

- 主要メソッド：
  - `format()`: `App\Calculations\Centipede\DTO\CentipedeSimulationResult` をフロントエンド用の配列に変換
  - `makeCognitiveUnitLatexText()`: LaTeX形式のテキストを生成
  - `makeChartData()`: チャート表示用データを生成

#### CentipedeDataCombiner クラス

CentipedeDataCombiner クラス (`App\Calculations\Centipede\CentipedeDataCombiner`) は、複数のシミュレーション結果を結合します。主な特徴：

- 主要メソッド：
  - `combine()`: 2つのプレイヤーのシミュレーション結果を結合

#### CentipedeSimulationResult クラス

CentipedeSimulationResult クラス (`App\Calculations\Centipede\DTO\CentipedeSimulationResult`) は、シミュレーション結果を保持するDTOです。主な特徴：

- シミュレーション結果のすべての値を保持
- ゲッターメソッドを提供
- 各ゲッターメソッドには適切なPHPDocを記載（プロパティの説明と戻り値の型）

#### 構造的な課題と改善点

- Centipedeシミュレータは、生のシミュレーション結果の算出と、それらの結合処理の2ステップのシミュレーションにより構成されています
- 結合処理は設定により行う場合と行わない場合があります
- シミュレーションが複数回行われるため、フォーマット処理と結合処理の順序が適正でないという課題があります
- 現在の実装では、`CentipedeFormatter::makeChartData()`が異なる場所で異なる入力形式で呼び出されており、データの流れが最適化されていません
- 改善案として、以下の処理順序が望ましいです：
  1. 生のシミュレーション結果を取得する
  2. 必要に応じて結合処理を行う（生データを使用）
  3. 最後にフォーマット処理を行う

**注意**: 現在のeval()の使用は、セキュリティ上のリスクがあるため、優先度の高い課題として安全な数式パーサーに置き換えることを検討しています。

## 分数計算の実装

計算クラスでは、精度を保つために分数計算を多用しています：

- `Phospr\Fraction` ライブラリを使用して分数を表現
- 分数の加減乗除は、対応するメソッド（`add()`, `subtract()`, `multiply()`, `divide()`）を使用
- 分数から浮動小数点数への変換は `toFloat()` メソッドを使用
- 分子と分母の取得には `getNumerator()` と `getDenominator()` メソッドを使用

### Fractionクラスの仕様と実装方針について

- `Phospr\Fraction` ライブラリでは$denominatorに0や負の値を入れると例外をスローします
- シミュレーションの実装上発生する事があるのは以下のケースです
  - new Fraction でインスタンスを生成するとき、第2引数に0や負の整数をセットする
  - Fractionのdivideメソッドを呼んだ時、引数に負の値を持つFractionインスタンスをセットする
- Requestクラスのバリデーションによりこの例外が発生しないパラメータである事を保証しているため、通常の処理では例外は発生しません
- そのため、Simulatorクラスで例外のコントロールは実装していません
- ただしユニットテスト側では例外が発生する事に対するテストケースを実装しています

## 配列型のPHPDoc

配列型をPHPDocに記載する際は、以下の方針に従います：

- 単に `array` と記載するのではなく、最低でも `array{key: mixed}` の形式で配列の構造を明示する
- 配列の要素の型が明確な場合は、`array{key: string, count: int}` のように具体的な型を指定する
- 連想配列でない場合は、`array<int, string>` のように記載する
- これにより、コードの可読性が向上し、型の不一致によるエラーを防止できる

**注意**: 既存コードの一部にはまだ詳細な配列型の記述が適用されていない箇所があります。例えば、`NashSimulationResult`クラスの`getMidpoint()`メソッドのPHPDocでは、戻り値の型が単に`array`と記載されています。今後のコード修正時に、ガイドラインに沿った形式に更新していく予定です。

## 関連ドキュメント

- [ユニットテスト](testing/unit-tests.md) - PHPユニットテストの実装方針
- [DTOガイドライン](dto/dto-guidelines.md) - DTOクラスのPHPDoc記述方法
