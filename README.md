# Mock Login API 

Este projeto é uma API simples para controle de login e recuperação de senha, desenvolvida para fins de estudo de integração Backend e manipulação de APIs REST com PHP.

---

## Configuração Local

* **Base URL:** `https://localhost/mock_api/api` 
    *(Nota: A porta pode variar dependendo da configuração do seu servidor local como XAMPP, WAMP ou Docker).*
* **Banco de Dados:** Configure as credenciais no arquivo `.env`. 
* **Ambiente:** Utilize o arquivo `.env.example` como base para criar o seu `.env`.

---
  
## Endpoints
### POST
#### `/register`
Realiza o cadastro de novos usuários.

**Corpo da Requisição (JSON):**
```json
  {
    "username": "JoaoSilva",
    "password": "senha",
    "name": "Joao Silva",
    "email": "joaosilva@email.com"
  }
```

  **Resposta de Sucesso** 
  ```json
  {
    "mensagem": "User created successfully",
    "username": "JoaoSilva"
  }
  ```

#### `/login`
Autentica o usuário no sistema

  **Corpo da Requisição (JSON):**
```json
  {
    "metodo": "email",
    "credencial": "joaosilva@email.com"
    "password": "teste"
  }
  ```
**Obs: O campo metodo aceita os valores "email" ou "username".**

  **Resposta de Sucesso** 
  ```json
  {
    "message": "user login successfully",
    "status": 200,
    "name": "Joao Silva"
  }
  ```
#### `/validateRecoveryCode`

  Inicia o processo de recuperação de senha enviando um e-mail via PHPMailer. As credenciais de SMTP devem estar configuradas no .env.

  **Corpo da Requisição (JSON):**
```json
  {
    "email": "joaosilva@email.com"
  }
  ```

  **Resposta de Sucesso** 
  ```json
  {
    "mensagem": "Recovery email sent"
  }
  ```

#### `/validateRecoveryCode`
Valida o código recebido por e-mail e define a nova senha.
  **Corpo da Requisição (JSON):**
```json
  {
    "recoveryCode": "867 400",
    "newPassword": "novaSenha"
  }
  ```

  **Resposta de Sucesso** 
  ```json
  {
    "mensagem": "Recovery code valid"
  }
  ```
---
### GET

#### `/username?username=JoaoSilva`

Verificar se ja existe um usuario com esse username.

**Resposta de Sucesso**
```json
{
    "mensagem": "User found",
    "user": "JoaoSilva"
}
```

---

### PUT

#### `/newPassword`

Alterar a senha do usuario.

  **Corpo da Requisição (JSON):**
```json
  {
    "username": "JoaoSilva",
    "password":"Jo@oSilv@",
    "newPassword":"J0@0S1lv@"
}
  ```

  **Resposta de Sucesso** 
  ```json
  {
    "mensagem": "Password changed"
  }
  ```

#### `/updateDataUser`

Alterar uma informação do usuario do usuario.

  **Corpo da Requisição (JSON):**
```json
  {
    "username": "JoaoSilva",
    "password":"J0@0S1lv@",
    "column": "username",
    "data": "JoaoSilvaJr"
}
  ```

  **Resposta de Sucesso** 
  ```json
  {
    "mensagem": "username changed"
  }
  ```
**Obs: O campo `"column` aceita os valores "email", "username", "number" e "password".**

---

### DELETE

#### `/deleteUser`

Deleta um usuario.

**Corpo da Requisição (JSON):**
```json
  {
    "username": "JoaoSilvaJr",
    "password":"J0@0S1lv@",
}
  ```

  **Resposta de Sucesso** 
  ```json
  {
    "mensagem": "User deleted"
  }
  ```

---
## Tecnologias
* PHP
   * PHPMailer (Envio de e-mails)
   * PHP Dotenv (Variáveis de ambiente)

* MySQL/MariaDB
