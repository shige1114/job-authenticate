# プロジェクト設計定義書: Laravel Modular Monolith (DDD)

## 1. 基本コンセプト
本プロジェクトは、ビジネスロジックの凝縮とモジュール間の疎結合を実現するため、**ドメイン駆動設計 (DDD)** と **モジュラーモノリス** を採用する。

- **モジュール独立性**: 各ビジネスモジュールは自身のドメイン知識を持ち、他モジュールに直接依存しない。
- **オーケストレーション**: 複数モジュールに跨るワークフローは `Orchestrator` が制御する。
- **インフラの外部化**: 技術的な詳細実装をモジュールの外に配置し、ビジネスロジック（Domain）をクリーンに保つ。

## 2. ディレクトリ構成定義


```text
/
├── Modules/
│   │   └── {ContextName}/
│   │       ├── database/           # [Framework固有]
│   │       │   ├── migrations/     # テーブル定義 (sales_ordering_orders 等)
│   │       │   ├── seeders/        # 初期データ・マスタ
│   │       │   └── factories/      # EloquentModel用ファクトリ
│   │       │
│   │       ├── routes/             # [Framework固有]
│   │       │   └── api.php         # パッケージの Presentation層へルーティング
│   │       │
│   │       └── packages/           # [ビジネスコア資産: DDD構成]
│   │           ├── README.md       # ユビキタス言語
│   │           ├── Docs/           # 仕様・設計資産
│   │           │   ├── openapi/    # API定義 (index.yaml, schemas/, paths/)
│   │           │   └── adr/        # アーキテクチャ決定記録
│   │           │
│   │           ├── Domain/         # ビジネスルール (Framework依存 0%)
│   │           │   ├── Models/     # Entities (ID識別) / ValueObjects (不変値)
│   │           │   │   ├── Entities/     # Entities (ID識別)
│   │           │   │   └── ValueObjects/     #  ValueObjects (不変値)
│   │           │   ├── Services/   # 複数モデルに跨るロジック
│   │           │   ├── Repositories/ # I/Oの抽象 (Interfaceのみ)
│   │           │   ├── Events/     # 発生した事象
│   │           │   ├── Exceptions/ # 業務例外
│   │           │   └── Factory/    # 複雑なEntity生成
│   │           │
│   │           ├── Application/    # ユースケース (進行役)
│   │           │   ├── UseCases/   # 実行単位 (Serviceクラス)
│   │           │   ├── DTOs/       # 入出力データの構造体
│   │           │   └── Subscribers/# Domain Event ハンドラ
│   │           │
│   │           ├── Infrastructure/ # Framework / Library への依存
│   │           │   ├── Persistence/ # Eloquent実装 / Repositories具体実装
│   │           │   ├── ExternalServices/ # 外部APIクライアント
│   │           │   ├── Messaging/  # EventBus / Queue実装
│   │           │   └── Providers/  # ★ Package専用の ServiceProvider
│   │           │
│   │           ├── Presentation/   # 外部インターフェース
│   │           │   ├── Controllers/# Application層への委譲
│   │           │   ├── Requests/   # Laravel FormRequest
│   │           │   ├── Resources/  # Laravel API Resource
│   │           │   └── Middlewares/# 固有ミドルウェア
│   │           │
│   │           └── Tests/          # テストコード
│   │               ├── Unit/       # Domain/Application単体テスト
│   │               └── Factories/  # Domain Model用ファクトリ
│   │
│   ├── Shared/                     # 共通基底クラス、定数
│   └── Orchestrator/               # コンテキスト間のワークフロー調整
│
├── infrastructure/                 # 全体共通Framework設定 (Auth/Logging)
└── app/                            # Provider登録用の外殻
