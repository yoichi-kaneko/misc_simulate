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

### Centipede クラス

Centipede クラスは、ムカデゲームのシミュレーションを行います。主な特徴：

- 複雑な数学的計算を `eval()` を使用して実行
- 主要メソッド：
  - `run()`: 複数パターンの計算を実行
  - `calculatePattern()`: 特定パターンの計算を実行
  - `unionCalculateData()`: 2つのプレイヤーのシミュレーション結果を合算
  - `calcCognitiveUnitValue()`: 認知単位の値を計算
  - `makeCognitiveUnitLatexText()`: LaTeX形式のテキストを生成
  - `makeChartData()`: チャート表示用データを生成

**注意**: 現在、Centipedeクラスは計算ロジックと表示ロジックが混在しています。将来的には、Nashクラスと同様に、計算処理（CentipedeSimulator）とフォーマット処理（CentipedeFormatter）に分離し、DTOを使用したリファクタリングを行う予定です。これにより、コードの保守性と拡張性が向上します。

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

## 関連ドキュメント

- [ユニットテスト](testing/unit-tests.md) - PHPユニットテストの実装方針
- [DTOガイドライン](dto/dto-guidelines.md) - DTOクラスのPHPDocと配列型の記述方法