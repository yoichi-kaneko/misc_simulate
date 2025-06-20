# ゲーム理論シミュレーションプロジェクト開発ガイドライン

このディレクトリには、ゲーム理論シミュレーションプロジェクトの開発に関するガイドラインが含まれています。

## 目次

### 全般
- [プロジェクト概要](general/project-overview.md) - プロジェクトの目的と構成

### バックエンド開発
- [PHP開発ガイドライン](backend/php-guidelines.md) - App\Calculationsクラスと分数計算の実装
- テスト関連
  - [ユニットテスト](backend/testing/unit-tests.md) - PHPユニットテストの実装方針
  - [フィーチャーテスト](backend/testing/feature-tests.md) - APIレスポンスのJSONアサーション規約
  - [カバレッジ](backend/testing/coverage.md) - PHPUnitのカバレッジレポート
- [DTOガイドライン](backend/dto/dto-guidelines.md) - DTOクラスのPHPDocと配列型の記述方法

### フロントエンド開発
- [TypeScript開発ガイドライン](frontend/typescript.md) - JavaScriptからTypeScriptへの移行
- [React開発ガイドライン](frontend/react.md) - jQueryからReactへの移行

### 開発プロセス
- [開発プロセス](general/development-process.md) - 新機能の追加と既存機能の修正
- [注意事項](general/cautions.md) - 開発時の注意点と不明確な点
