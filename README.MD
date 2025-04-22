# PHP REST API com JWT e MySQL

![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![JWT](https://img.shields.io/badge/JWT-Authentication-green)
![REST](https://img.shields.io/badge/REST-API-yellow)

Uma API REST elegante e poderosa construída com PHP puro, utilizando autenticação JWT e banco de dados MySQL. Este projeto implementa um CRUD completo seguindo boas práticas de desenvolvimento.

## 🚀 Funcionalidades

- ✅ Sistema de autenticação com JWT
- ✅ Operações CRUD para usuários
- ✅ Arquitetura organizada e escalável
- ✅ Middleware de autenticação
- ✅ Validação de dados
- ✅ Gerenciamento de erros

## 📚 Estrutura do Projeto

```
projeto-api/
├── .htaccess                # Configurações de rewrite para URLs amigáveis
├── composer.json            # Dependências do projeto
├── index.php                # Ponto de entrada da aplicação e router
├── config/
│   └── Database.php         # Configuração da conexão com o banco de dados
├── controllers/
│   ├── AuthController.php   # Controlador para autenticação
│   └── UserController.php   # Controlador para operações de usuários
├── models/
│   └── User.php             # Modelo de dados de usuário
├── utils/
│   └── JwtHandler.php       # Classe para gerenciamento de JWT
└── middleware/
    └── AuthMiddleware.php   # Middleware para verificação de autenticação
```

## 📋 Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Servidor web (Apache ou Nginx)

## ⚙️ Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/projeto-api.git
cd projeto-api
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configuração do banco de dados
Edite o arquivo `config/Database.php` com suas credenciais:

```php
private $host = "localhost";
private $database_name = "api_db";
private $username = "seu_usuario";
private $password = "sua_senha";
```

### 4. Criação do banco de dados
```sql
CREATE DATABASE api_db;
USE api_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5. Configuração do servidor web
Para Apache, certifique-se de que o arquivo `.htaccess` esteja na raiz do projeto.

Para Nginx, adicione a seguinte configuração:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🔒 Configuração de Segurança

Edite o arquivo `utils/JwtHandler.php` e altere a chave secreta:

```php
private $secretKey = "sua_chave_secreta_muito_segura";
```

**Importante:** Em ambiente de produção, mova esta chave para uma variável de ambiente.

## 🚀 Uso da API

### Autenticação

#### Registro
```http
POST /auth/register
Content-Type: application/json

{
  "name": "Usuário Teste",
  "email": "usuario@teste.com",
  "password": "senha123"
}
```

#### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "usuario@teste.com",
  "password": "senha123"
}
```

Resposta (exemplo):
```json
{
  "status": 1,
  "message": "Login realizado com sucesso",
  "token": "eyJ0eXAiOiJKV...",
  "user": {
    "id": 1,
    "name": "Usuário Teste",
    "email": "usuario@teste.com"
  }
}
```

### Endpoints de Usuários

**Nota:** Todos os endpoints abaixo requerem autenticação. Adicione o header:
```
Authorization: Bearer {seu_token_jwt}
```

#### Listar todos os usuários
```http
GET /users
```

#### Obter usuário por ID
```http
GET /users/{id}
```

#### Criar novo usuário
```http
POST /users
Content-Type: application/json

{
  "name": "Novo Usuário",
  "email": "novo@usuario.com",
  "password": "senha456"
}
```

#### Atualizar usuário
```http
PUT /users/{id}
Content-Type: application/json

{
  "name": "Nome Atualizado",
  "email": "email@atualizado.com"
}
```

#### Excluir usuário
```http
DELETE /users/{id}
```

## 📝 Notas importantes

1. A API implementa verificação de permissões básica: usuários só podem atualizar ou excluir seus próprios dados.
2. Para aumentar a segurança, considere:
   - Implementar limitação de taxa (rate limiting)
   - Usar HTTPS em produção
   - Adicionar validação mais robusta dos dados de entrada
   - Implementar sistema de logs

## 🛠️ Personalizações

### Adicionar novos recursos

Para adicionar um novo recurso (por exemplo, "produtos"):

1. Crie uma nova tabela no banco de dados
2. Crie um modelo em `models/Product.php`
3. Crie um controlador em `controllers/ProductController.php`
4. Adicione as rotas em `index.php`

### Alterar tempo de expiração do token

Edite a linha a seguir em `utils/JwtHandler.php`:

```php
$this->expiry = $this->issuedAt + 3600; // Token válido por 1 hora
```

## 🔄 Fluxo de autenticação

1. O usuário envia credenciais (email/senha)
2. O sistema verifica as credenciais no banco de dados
3. Se válidas, gera um token JWT contendo o ID do usuário
4. O token é retornado ao usuário
5. O usuário inclui o token nos headers das requisições subsequentes
6. O middleware valida o token antes de permitir acesso aos endpoints protegidos

## 🤝 Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## 📜 Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para detalhes.
