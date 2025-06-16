# TypeScript開発ガイドライン

## JavaScriptからTypeScriptへの移行

現在、JavaScriptからTypeScriptへの移行を進めています：

- 新規コードはTypeScriptで記述
- 既存のJavaScriptコードは段階的にTypeScriptに移行中
- `tsconfig.json` で型チェックの設定を管理

### TypeScript化の進捗状況

- Reactコンポーネントの実装にTypeScriptを使用
- 主要な計算ロジックのTypeScript化が完了
- 残りのユーティリティ関数やヘルパー関数のTypeScript化を進行中
- 今後6ヶ月以内に全てのJavaScriptコードをTypeScriptに移行する予定

## TypeScript実装例（nash.ts）

TypeScriptでは、以下のような実装パターンを採用しています：

- 型定義を使用した関数宣言（例：`function reset(): void`）
- ES6モジュールインポート構文の使用
- イベントハンドラの実装

```typescript
// 型定義の例
interface NashParameters {
  alpha_x: number;
  alpha_y: number;
  beta_x: number;
  beta_y: number;
}

// 関数宣言の例
function calculateNash(params: NashParameters): void {
  // 計算処理
}

// イベントハンドラの例
function handleSubmit(event: React.FormEvent<HTMLFormElement>): void {
  event.preventDefault();
  // フォーム送信処理
}
```

## JavaScript実装例（centipede.js）

現在のJavaScriptコードでは、以下のパターンを使用しています：

- ES6モジュールインポート構文の使用（例：`import {beforeCalculate} from "../functions/calculate"`）
- jQueryを使用したDOM操作とイベントハンドリング
- ブラウザ検出とチャートダウンロード機能

```javascript
// ES6モジュールインポートの例
import {beforeCalculate} from "../functions/calculate";

// jQueryを使用したイベントハンドリングの例
$("#calculate-button").on("click", function() {
  // 計算処理
});

// ブラウザ検出の例
const isChrome = /Chrome/.test(navigator.userAgent);
```

## TypeScript移行のガイドライン

### 新規コードの作成

新規コードを作成する際は、以下のガイドラインに従ってください：

1. **ファイル拡張子**: `.ts` または `.tsx`（Reactコンポーネントの場合）を使用
2. **型定義**: 関数の引数と戻り値に型アノテーションを追加
3. **インターフェース**: 複雑なオブジェクト構造にはインターフェースを定義
4. **列挙型**: 固定値のセットには `enum` を使用
5. **ジェネリクス**: 再利用可能なコンポーネントやユーティリティには適切にジェネリクスを活用

### 既存コードの移行

既存のJavaScriptコードをTypeScriptに移行する際は、以下の手順に従ってください：

1. ファイル拡張子を `.js` から `.ts` に変更
2. 最小限の型アノテーションを追加して、コンパイルエラーを解消
3. `any` 型の使用を最小限に抑え、徐々に具体的な型に置き換える
4. jQueryの使用を段階的に減らし、TypeScriptネイティブのアプローチに置き換える
5. テストを実行して、機能が正しく動作することを確認

### コード品質の確保

TypeScriptコードの品質を確保するために、以下の施策を実施しています：

1. **ESLint**: TypeScript用のESLintルールを適用
2. **Prettier**: コードフォーマットの一貫性を確保
3. **tsconfig.json**: 厳格な型チェック設定を有効化
   - `"strict": true`
   - `"noImplicitAny": true`
   - `"strictNullChecks": true`

## 関連ドキュメント

- [React開発ガイドライン](react.md) - jQueryからReactへの移行
- [フロントエンド共通ガイドライン](common.md) - フロントエンド開発の共通ガイドライン