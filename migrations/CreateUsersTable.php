<?php
namespace Api\Migrations;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $query = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        try {
            $this->conn->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erro ao criar tabela users: " . $e->getMessage();
            return false;
        }
    }
    
    public function down()
    {
        $query = "DROP TABLE IF EXISTS users";
        
        try {
            $this->conn->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erro ao remover tabela users: " . $e->getMessage();
            return false;
        }
    }
}