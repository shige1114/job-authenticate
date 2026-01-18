# Gemini 開発プロセス定義

本プロジェクトにおいて Gemini に設計・実装を依頼する際は、以下の 5 ステップを順に踏むものとする。各ステップで成果物を作成し、ユーザーの承認を得てから次のステップへ進むこと。

## Step 1: ドメインモデリング
- **内容**: ビジネスルールの理解、言葉の定義（ユビキタス言語）、ドメイン構造の確定。
- **成果物**: 
    - `modules/{Domain}/{Context}/Docs/language.md` の作成・更新案。
    - ドメインモデル（クラス図・関連図等）の提示。

## Step 2: 設計
- **内容**: モジュール間およびクラス間の動的な振る舞い、連携フローの設計。
- **成果物**: 
    - `modules/{Domain}/{Context}/Docs/diagrams/` に保管するための **Mermaid形式シーケンス図**。

## Step 3: TDDによる開発（テスト作成・モック利用）
- **内容**: 実装前に「正解」を定義する。この段階で DB や外部サービスの実装は不要とする。
- **モックの活用**: `Domain/Repositories/` 等で定義した **Interface をモック化**し、Infrastructure 層に依存せずに Application/Domain 層のテストを実行可能にする。
- **成果物**: 
    - `modules/{Domain}/{Context}/Tests/` 配下に配置する **PHPUnit または Pest によるテストコード**。

## Step 4: 実装およびプログラム変更
- **内容**: テストをパスさせるための最小限の実装、およびリファクタリング。
- **ルール**: 既存プログラムを変更・拡張する場合も、**必ず先にテストを更新・作成**し、その後にコード本体を修正すること。
- **成果物**: 
    - `modules/` 配下のソースコード（Domain/Application/Presentation等）。

## Step 5: Infrastructureの実装
- **内容**: Repositoryの具象クラス、Eloquentモデル、外部APIクライアント等の技術的詳細の実装。
- **成果物**: 
    - `infrastructure/` 配下のソースコード。
    - 各コンテキストの `ServiceProvider` 等での DI（Dependency Injection）設定の更新案。
