Projeto para estudo
API simples de controle de login

   URL : `https://localhost/mock_api`
  
  A url vai variar de acordo com a porta da seu servidor local

  configure seu banco de dado no `.env`, utilize o `.env EXEMPLO` como base para configurar.
  
Endpoints
POST
* `/register`

  campos: `
  {
    "username": "JoaoSilva",
    "password": "senha",
    "name": "Joao Silva",
    "email": "joaosilva@email.com"
  }
  `

  retorno de sucesso: `
  {
    "mensagem": "User created successfully",
    "username": "JoaoSilva"
  }
  `
* `/login`

  campos: `
  {
    "metodo": "email", //email ou username
    "credencial": "joaosilva@email.com"
    "password": "teste"
  }
  `

  retorno de sucesso: `
  {
    "message": "user login successfully",
    "status": 200,
    "name": "Joao Silva"
  }
  `

* `/validateRecoveryCode`

  Utiliza o PHPMailer para enviar o email com o codigo de verificação, as credenciais do Email devem ser configuradas no arquivo `.env`, utilize o `.env Exemplo` como base.

  campos: `
  {
    "email": "joaosilva@email.com"
  }
  `

  retorno de sucesso: `
  {
    "mensagem": "Recovery email sent"
  }
  `

* `/validateRecoveryCode`

  campos: `
  {
    "recoveryCode": "867 400",
    "newPassword": "novaSenha"
  }
  `

  retorno de sucesso: `
  {
    "mensagem": "Recovery code valid"
  }
  `

GET
