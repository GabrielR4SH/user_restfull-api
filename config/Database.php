<?php
namespace Api\Config;

use Dotenv\Dotenv;

class Database {
    private $host;
    private $database_name;
    private $username;
    private $password;
    public $conn;
    
    public function __construct() {
        // Carrega variáveis de ambiente do arquivo .env
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
        
        // Obtém credenciais do banco de dados das variáveis de ambiente
        $this->host = $_ENV['DB_HOST'];
        $this->database_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new \PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(\PDOException $e) {
            // Em produção, evite exibir detalhes do erro diretamente
            error_log("Erro na conexão com o banco: " . $e->getMessage());
            throw new \Exception("Erro ao conectar ao banco de dados");
        }
        
        return $this->conn;
    }
    
    // Método de utilidade para criar as tabelas necessárias
    public function setupDatabase() {
        $query = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->conn->exec($query);
    }
}