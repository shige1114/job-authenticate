```mermaid
sequenceDiagram
    actor User
    participant Presentation
    participant Application
    participant Domain
    participant Infrastructure
    participant UserRepository

    Note over User, Presentation: User redirected to Password Setting Page with token (from email verification)
    User->>Presentation: パスワード設定画面表示 (token)
    Presentation->>User: パスワード入力フォーム

    User->>Presentation: パスワード入力 (password, password_confirmation)
    Presentation->>Application: setPassword (token, password, password_confirmation)
    Application->>Application: パスワードの検証 (強度, 一致)
    alt 検証成功
        Application->>Infrastructure: ユーザー検索 (token)
        Infrastructure-->>Application: 仮UserEntity
        alt 仮UserEntityが見つからない or 無効
            Application-->>Presentation: エラー (無効なトークン or 期限切れ)
            Presentation-->>User: エラーメッセージ表示
        else 仮UserEntityが有効
            Application->>Domain: User.updatePassword (password)
            Domain->>Domain: パスワードの暗号化
            Domain-->>Application: UserEntity (パスワード更新済み)
            Application->>Infrastructure: UserRepository.save(UserEntity)
            Infrastructure-->>Application: UserEntity (保存済み)
            Application->>Infrastructure: PendingEmailVerification削除 (token) # If not deleted before
            Application-->>Presentation: 登録完了
            Presentation-->>User: 登録完了メッセージ表示, ログインページへリダイレクト
        end
    else 検証失敗 (setPassword)
        Application-->>Presentation: エラー (パスワードポリシー違反 or 不一致)
        Presentation-->>User: エラーメッセージ表示
    end
```

### パスワード登録シーケンスの説明:

1.  **パスワード設定ページへのリダイレクト:** ユーザーはメール認証完了後、トークン付きでパスワード設定ページへリダイレクトされます。
2.  **ユーザーによるパスワード入力:** `User` は `Presentation` レイヤー (パスワード設定フォーム) に、設定したいパスワードと確認用パスワードを入力します。
3.  **Presentation から Application へ:** `Presentation` レイヤーはこのリクエスト (トークン、パスワード、確認用パスワード) を `Application` レイヤー (`SetPasswordUseCase` のようなユースケース) に転送します。
4.  **Application によるパスワードの検証とユーザー更新:**
    *   `Application` レイヤーは、提供されたパスワードがセキュリティポリシーを満たしているか、および確認用パスワードと一致するかを検証します。
    *   トークンを使用して、対応する仮 `User` エンティティを `Infrastructure` レイヤー経由で検索します。
    *   仮 `User` エンティティが見つからない、または無効な場合（例: トークンが期限切れ）、エラーを返します。
    *   有効な `User` エンティティが見つかった場合、`Domain` レイヤーを通じてその `User` エンティティのパスワードを更新し、暗号化します。
    *   `Application` レイヤーは、更新された `User` エンティティを `Infrastructure` レイヤーの `UserRepository` を介して永続化します。
    *   必要に応じて、`PendingEmailVerification` レコードも削除します（メール検証ステップでまだ削除されていない場合）。
5.  **Application から Presentation へ:** 処理が成功した場合、`Application` レイヤーは登録完了のステータスを `Presentation` レイヤーに返します。失敗した場合は、適切なエラーメッセージを返します。
6.  **ユーザーへのフィードバックとリダイレクト:**
    *   `Presentation` レイヤーは、登録が成功したことを `User` に表示し、ログインページへリダイレクトします。
    *   失敗した場合は、エラーメッセージを `User` に表示します。
