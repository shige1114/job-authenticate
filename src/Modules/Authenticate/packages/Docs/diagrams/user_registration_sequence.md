```mermaid
sequenceDiagram
    actor User
    participant Presentation
    participant Application
    participant Domain
    participant Infrastructure
    participant UserRepository #New for User creation

    User->>Presentation: 登録 (メール)
    Presentation->>Application: requestEmailVerification
    Application->>Application: 登録データの検証
    alt 検証成功
        Application->>Application: 6桁の認証コード生成
        Application->>Infrastructure: PendingEmailVerification保存
        Infrastructure-->>Application: PendingEmailVerificationEntity
        Application->>Infrastructure: 認証メール送信 (6桁コード)
        Infrastructure-->>Application: メール送信確認
        Application-->>Presentation: メール認証リクエスト済み
        Presentation-->>User: 成功メッセージ表示、メール確認とコード入力の指示
    else 検証失敗 (RequestEmailVerification)
        Application-->>Presentation: 検証エラー
        Presentation-->>User: エラーメッセージ表示
    end

    User->>Presentation: 6桁コード入力
    Presentation->>Application: verifyEmailCode (6桁コード)
    Application->>Infrastructure: PendingEmailVerification検索 (コード)
    Infrastructure-->>Application: PendingEmailVerificationEntity or null
    alt コード不一致 or 期限切れ
        Application-->>Presentation: 検証エラー
        Presentation-->>User: エラーメッセージ表示
    else コード一致 and 期限内
        Application->>Infrastructure: PendingEmailVerification削除
        Infrastructure-->>Application: 削除完了
        Application->>Domain: 仮Userエンティティ作成 (メール検証済み)
        Application->>Infrastructure: 仮User保存
        Infrastructure-->>Application: UserID
        Application-->>Presentation: UserID / セッショントークン
        Presentation-->>User: パスワード設定ページへリダイレクト (UserID/トークン付き)
    end
```

### ユーザー登録シーケンスの説明:

1.  **ユーザーによる登録開始:** `User` は `Presentation` レイヤー (例: ウェブフォーム) にメールアドレスのみを提供します。
2.  **Presentation から Application へ:** `Presentation` レイヤーはこのリクエストを `Application` レイヤー (特に `RequestEmailVerificationUseCase` のようなユースケース) に転送します。
3.  **Application による認証コードの発行と送信:** `Application` レイヤーは、ユーザー認証に必要な認証コードを発行し、ユーザーに送信します。
4.  **6桁の認証コードの生成と登録保留メール認証の保存:** 検証が成功した場合、`Application` レイヤーは6桁の認証コードを生成し、メールアドレスとこのコードを含む登録保留メール認証レコードを `Infrastructure` レイヤーに保存するよう指示します。この段階ではパスワードは設定されません。
5.  **認証メールの送信:** `Application` レイヤーは、生成された6桁の認証コードを含む認証メールを `Infrastructure` レイヤーに送信するよう指示します。
6.  **Infrastructure によるメール送信:** `Infrastructure` レイヤーが実際のメール送信を処理します。
7.  **ユーザーによるコード入力と検証リクエスト:** `User` は `Presentation` レイヤーに受信した6桁の認証コードを提供し、検証をリクエストします。
8.  **Presentation から Application へ (VerifyEmailUseCase):** `Presentation` レイヤーはこのリクエストを `Application` レイヤー (新しい `VerifyEmailUseCase` のようなユースケース) に転送します。
9.  **Application による認証コードの検証と仮 `User` の作成:** `Application` レイヤーは、提供されたコードが `PendingEmailVerification` レコードと一致し、かつ有効期限内であることを検証します。検証に成功した場合、対応する `PendingEmailVerification` レコードを削除し、**パスワード未設定の仮 `User` エンティティを作成します。この `User` エンティティはメール検証済みとしてマークされます。**
10. **Application から Presentation へ:** `Application` レイヤーは、作成された仮 `User` のID（またはパスワード設定用のセッショントークン）を `Presentation` レイヤーに返します。
11. **ユーザーへのフィードバックとパスワード設定へのリダイレクト:** `Presentation` レイヤーは `User` に成功メッセージを表示し、受け取ったID（またはセッショントークン）を使用してパスワード設定ページへリダイレクトします。
12. **エラーハンドリング:** いずれかの時点で検証が失敗した場合、適切なエラーメッセージが各レイヤーを通じて `User` に返されます。
