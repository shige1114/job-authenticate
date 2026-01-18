```mermaid
sequenceDiagram
    actor User
    participant RegisterController
    participant RegisterService
    participant UserRepository
    participant EloquentUser
    participant HashService

    User->>+RegisterController: POST /register (name, email, password, password_confirmation)
    RegisterController->>+RegisterService: register(name, email, password)
    RegisterService->>RegisterService: Validate data (e.g., email unique, password match)
    alt Email already exists
        RegisterService-->>-RegisterController: RegistrationException (e.g., EmailAlreadyExistsException)
        RegisterController-->>-User: Error Response
    else Validation passes
        RegisterService->>+UserRepository: findByEmail(email)
        UserRepository-->>-RegisterService: null (email is unique)
        RegisterService->>+HashService: make(password)
        HashService-->>-RegisterService: hashedPassword
        RegisterService->>UserRepository: save(name, email, hashedPassword)
        UserRepository->>+EloquentUser: create(name, email, hashedPassword)
        EloquentUser-->>-UserRepository: created EloquentUser
        UserRepository-->>-RegisterService: created EloquentUser
        RegisterService-->>-RegisterController: created EloquentUser
        RegisterController-->>-User: Success Response (e.g., JWT Token, or redirect)
    end
```
