# React開発ガイドライン

## jQueryからReactへの移行

現在、jQueryからReactへの移行は進行中です：

- 新規コンポーネントはReactで実装
- 既存のjQueryコードは段階的にReactコンポーネントに置き換え中
- jQueryの使用は最小限に抑え、Reactの状態管理を活用
- jQueryで書かれたコードは、段階的にReactのライフサイクルメソッドに置き換え
- 例えば、nash.tsではリセットボタンの機能をjQueryからReactに移行しました

### React導入の進捗状況

- ナッシュシミュレーション画面のコンポーネント化が完了
- ムカデゲーム画面のReactコンポーネント化を進行中
- 共通UIコンポーネントのライブラリ化を検討中
- 今後1年以内に全てのフロントエンドコードをReactベースに移行する予定

## Reactコンポーネントの設計原則

### コンポーネント分割の方針

- 単一責任の原則に従い、1つのコンポーネントは1つの責任のみを持つ
- 再利用可能なUIパーツは共通コンポーネントとして切り出す
- ビジネスロジックとUIを分離する（Container/Presentational パターン）
- 複雑なフォームは小さなコンポーネントに分割する

### 状態管理

- ローカルな状態は `useState` フックを使用
- 複数のコンポーネント間で共有する状態は `useContext` を使用
- 複雑な状態遷移には `useReducer` を使用
- グローバルな状態管理が必要な場合は、将来的にReduxの導入を検討

### TypeScriptとの統合

- Reactコンポーネントには適切な型定義を付与
- Propsには明示的なインターフェースを定義
- イベントハンドラには適切なイベント型を使用
- 条件付きレンダリングには型ガードを活用

```tsx
// Propsのインターフェース定義
interface CalculatorProps {
  initialValues?: {
    alpha_x: number;
    alpha_y: number;
  };
  onCalculate: (result: CalculationResult) => void;
}

// Reactコンポーネントの例
const Calculator: React.FC<CalculatorProps> = ({ initialValues, onCalculate }) => {
  const [values, setValues] = useState(initialValues || { alpha_x: 0, alpha_y: 0 });
  
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // 計算処理
    onCalculate(result);
  };
  
  return (
    <form onSubmit={handleSubmit}>
      {/* フォーム要素 */}
    </form>
  );
};
```

## jQueryからReactへの移行ガイドライン

### 段階的な移行アプローチ

1. **分析フェーズ**:
   - 既存のjQueryコードの機能を特定
   - コンポーネント候補を識別
   - 依存関係を把握

2. **コンポーネント設計**:
   - Reactコンポーネント階層を設計
   - 状態管理戦略を決定
   - TypeScriptインターフェースを定義

3. **実装フェーズ**:
   - 最小限のReactコンポーネントを実装
   - jQueryコードと並行して動作させる
   - 段階的に機能を移行

4. **検証フェーズ**:
   - 機能の同等性を検証
   - パフォーマンスを測定
   - ユーザー体験の向上を確認

5. **完全移行**:
   - jQueryコードを削除
   - Reactコンポーネントを最適化
   - ドキュメントを更新

### 共存期間中の注意点

- jQueryとReactの両方が同じDOM要素を操作しないようにする
- イベントハンドラの重複登録に注意
- グローバル変数や関数の使用を最小限に抑える
- Reactコンポーネントのマウント/アンマウントタイミングでjQueryイベントリスナーを適切に管理

## ベストプラクティス

- コンポーネントは小さく保ち、1ファイルあたり300行を超えないようにする
- 複雑なロジックはカスタムフックに抽出する
- メモ化（`React.memo`, `useMemo`, `useCallback`）を適切に使用してパフォーマンスを最適化
- エラー境界を使用して、コンポーネントのクラッシュを適切に処理する
- アクセシビリティ（WAI-ARIA）に配慮したマークアップを使用する

## 関連ドキュメント

- [TypeScript開発ガイドライン](typescript.md) - JavaScriptからTypeScriptへの移行
- [フロントエンド共通ガイドライン](common.md) - フロントエンド開発の共通ガイドライン