# プロジェクト設計定義書: Laravel Modular Monolith (DDD)

## 1. 基本コンセプト
本プロジェクトは、ビジネスロジックの凝縮とモジュール間の疎結合を実現するため、**ドメイン駆動設計 (DDD)** と **モジュラーモノリス** を採用する。

- **モジュール独立性**: 各ビジネスモジュールは自身のドメイン知識を持ち、他モジュールに直接依存しない。
- **オーケストレーション**: 複数モジュールに跨るワークフローは `Orchestrator` が制御する。
- **インフラの外部化**: 技術的な詳細実装をモジュールの外に配置し、ビジネスロジック（Domain）をクリーンに保つ。

## 2. ディレクトリ構成定義

```text
/
├── modules/
│   ├── Orchestrator/              # モジュール間のワークフロー・調整役
│   │   ├── Application/           # 複数モジュールの Service を組み合わせたユースケース
│   │   └── Presentation/          # 複合的な処理のエンドポイント (Controller等)
│   │
│   ├── {DomainName}/              # ビジネス領域 (例: Sales, Logistics)
│   │   └── {ContextName}/         # 境界づけられたコンテキスト (例: Ordering, Inventory)
│   │       ├── Docs/              # ★ コンテキスト固有のドキュメント
│   │       │   ├── openapi/       # API定義 (YAML/JSON)
│   │       │   └── language/      # ユビキタス言語（用語集）
│   │       │
│   │       ├── Domain/            # ★ ビジネスロジックの本質
│   │       │   ├── Models/        # Entity, ValueObject
│   │       │   ├── Services/      # Domain Services
│   │       │   ├── Repositories/  # Repository Interfaces（保存・取得の定義）
│   │       │   ├── Events/        # Domain Events
│   │       │   └── Exceptions/    # Domain Exceptions
│   │       │
│   │       ├── Application/       # ApplicationService (Orchestratorからの呼出窓口)
│   │       ├── Presentation/      # コンテキスト単体で完結する機能のエンドポイント
│   │       └── Tests/             # コンテキスト内のテスト (Unit, Integration)
│   │
│   └── Shared/                    # 全モジュール共通のインターフェースや定数
│
├── infrastructure/                # 技術的実装の詳細（モジュールの外側に配置）
│   ├── Persistence/               # Eloquentモデルの実体、Repositoryの具象実装
│   ├── Messaging/                 # EventBus, Queue, Notificationの実装
│   ├── ExternalServices/          # 決済、配送業者等の外部APIクライアント
│   └── Shared/                    # 共通インフラ基盤
│
└── app/                           # Laravel標準基盤（Shared Kernel・共通基盤のみ）
