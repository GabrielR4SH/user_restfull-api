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
- ✅ Sistema de migrations
- ✅ Validação de dados
- ✅ Gerenciamento de erros

## 📚 Estrutura do Projeto

```
user_restfull-api/
├── .env                    # Variáveis de ambiente (não incluir no git)
├── .env.example            # Exemplo das variáveis necessárias
├── .gitignore              # Arquivos a ignorar no git
├── .htaccess               # Configurações de rewrite para URLs amigáveis
├── composer.json           # Dependências do projeto
├── index.php               # Ponto de entrada da aplicação e router
├── migrate.php             # Script para executar migrations
├── config/
│   └── Database.php        # Configuração da conexão com o banco de dados
├── controllers/
│   ├── AuthController.php  # Controlador para autenticação
│   └── UserController.php  # Controlador para operações de usuários
├── migrations/
│   ├── Migration.php       # Classe base para migrations
│   ├── MigrationManager.php # Gerenciador de migrations
│   └── CreateUsersTable.php # Migration para criar tabela de usuários
├── models/
│   └── User.php            # Modelo de dados de usuário
├── utils/
│   └── JwtHandler.php      # Classe para gerenciamento de JWT
└── middleware/
    └── AuthMiddleware.php  # Middleware para verificação de autenticação
```

## 📋 Pré-requisitos

- XAMPP com PHP 8.0 ou superior
- MySQL 5.7 ou superior (incluído no XAMPP)
- Composer

## ⚙️ Instalação e Configuração com XAMPP

### 1. Preparar o diretório do projeto
Coloque os arquivos do projeto em `C:\xampp\htdocs\user_restfull-api\` (Windows) ou `/opt/lampp/htdocs/user_restfull-api/` (Linux).

### 2. Instalar dependências com Composer
Abra o terminal/prompt de comando, navegue até a pasta do projeto e execute:
```bash
cd C:\xampp\htdocs\user_restfull-api
composer install
```

### 3. Configurar variáveis de ambiente
Crie um arquivo `.env` na raiz do projeto:

```bash
# Copie o arquivo .env.example
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:
```
DB_HOST=localhost
DB_NAME=api_db
DB_USER=root
DB_PASSWORD=
JWT_SECRET=sua_chave_secreta_muito_segura
```

### 4. Criar o banco de dados
Acesse o phpMyAdmin em `http://localhost/phpmyadmin` e execute:

```sql
CREATE DATABASE api_db;
```

Ou use o MySQL por linha de comando:
```bash
mysql -u root -p
CREATE DATABASE api_db;
EXIT;
```

### 5. Executar as migrations

#### Via linha de comando (recomendado):
```bash
php migrate.php
```

#### Via navegador:
Acesse `http://localhost/user_restfull-api/migrate.php` no seu navegador.

## 🛠️ Sistema de Migrations

Este projeto inclui um sistema de migrations que permite criar e gerenciar a estrutura do banco de dados de forma controlada.

### Executar as migrations
```bash
php migrate.php
```

### Reverter a última migration
```bash
php migrate.php rollback
```

### Criar uma nova migration
1. Crie uma nova classe em `migrations/` que estenda a classe `Migration`
2. Implemente os métodos `up()` (para criar/alterar) e `down()` (para reverter)
3. Registre sua migration no arquivo `migrations/MigrationManager.php`

Exemplo de uma nova migration:

```php
<?php
namespace Api\Migrations;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $query = "
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        try {
            $this->conn->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erro ao criar tabela products: " . $e->getMessage();
            return false;
        }
    }
    
    public function down()
    {
        $query = "DROP TABLE IF EXISTS products";
        
        try {
            $this->conn->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erro ao remover tabela products: " . $e->getMessage();
            return false;
        }
    }
}
```

Depois, adicione a nova migration ao MigrationManager:

```php
// Em MigrationManager.php
$this->migrations = [
    CreateUsersTable::class,
    CreateProductsTable::class  // Nova migration
];
```

## 🧪 Testando a API no XAMPP

### Configuração do XAMPP

1. **Certifique-se que o mod_rewrite está habilitado**:
   - No painel do XAMPP, clique em "Config" ao lado do Apache
   - Escolha "Apache (httpd.conf)"
   - Verifique se a linha `LoadModule rewrite_module modules/mod_rewrite.so` está sem # no início
   - Certifique-se que na configuração do diretório htdocs esteja `AllowOverride All`
   - Reinicie o Apache

2. **Correção de URLs para subdiretórios**:
   Se o projeto está num subdiretório como `user_restfull-api`, certifique-se que o roteamento do `index.php` está considerando esse subdiretório.

### Testando os endpoints

Use ferramentas como Postman, Insomnia ou curl para testar a API.

1. **Registrar um usuário**
```http
POST http://localhost/user_restfull-api/auth/register
Content-Type: application/json

{
  "name": "Usuário Teste",
  "email": "usuario@teste.com",
  "password": "senha123"
}
```

2. **Fazer login**
```http
POST http://localhost/user_restfull-api/auth/login
Content-Type: application/json

{
  "email": "usuario@teste.com",
  "password": "senha123"
}
```
Resposta:
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

3. **Listar usuários** (requer token)
```http
GET http://localhost/user_restfull-api/users
Authorization: Bearer seu_token_jwt
```

4. **Obter um usuário específico** (requer token)
```http
GET http://localhost/user_restfull-api/users/1
Authorization: Bearer seu_token_jwt
```

5. **Atualizar usuário** (requer token)
```http
PUT http://localhost/user_restfull-api/users/1
Content-Type: application/json
Authorization: Bearer seu_token_jwt

{
  "name": "Nome Atualizado",
  "email": "email@atualizado.com"
}
```

6. **Excluir usuário** (requer token)
```http
DELETE http://localhost/user_restfull-api/users/1
Authorization: Bearer seu_token_jwt
```

## 🔒 Segurança

### Boas práticas implementadas
- Armazenamento de senhas com hash seguro (password_hash)
- Autenticação com JWT
- Validação de dados de entrada
- Utilização de variáveis de ambiente para credenciais sensíveis
- Sistema de migrations controlado

### Sugestões para melhorias adicionais
- Implementar HTTPS em produção
- Configurar CORS adequadamente
- Adicionar rate limiting para prevenir ataques de força bruta
- Implementar logs detalhados

## 🧩 Como estender o projeto

### Adicionar novos recursos

Para adicionar um novo recurso (ex: "produtos"):

1. Crie uma nova migration:
```php
// migrations/CreateProductsTable.php
// (Ver exemplo acima)
```

2. Adicione a migration ao MigrationManager e execute-a:
```bash
php migrate.php
```

3. Crie o modelo em `models/Product.php`
4. Crie o controlador em `controllers/ProductController.php`
5. Adicione as rotas em `index.php`

## 📚 Resolução de problemas comuns

### Erro 404 - Página não encontrada
- Verifique se o módulo rewrite está ativado no Apache
- Confirme que o arquivo .htaccess está na raiz do projeto
- Verifique se a configuração `AllowOverride All` está definida no httpd.conf
- Verifique se o router está lidando corretamente com o subdiretório do projeto

### Erro "Recurso não encontrado"
- Verifique se a URL está correta, incluindo o nome do subdiretório
- Confirme se o router está processando corretamente a URL, removendo o nome do subdiretório antes de procurar pelas rotas

### Erro de autenticação
- Verifique se o token JWT está sendo enviado corretamente no cabeçalho
- Confirme se a chave secreta JWT é a mesma usada para gerar e validar tokens
- Verifique se o token não está expirado

### Erro de conexão com o banco de dados
- Confirme que as credenciais no arquivo .env estão corretas
- Verifique se o serviço MySQL está em execução
- Confirme que o banco de dados existe

## 🤝 Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.


