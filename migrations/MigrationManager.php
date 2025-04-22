<?php
namespace Api\Migrations;

use Api\Config\Database;

class MigrationManager
{
    private $conn;
    private $migrations = [];
    
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->createMigrationsTable();
        
        // Registrar todas as migrations aqui
        $this->migrations = [
            CreateUsersTable::class
        ];
    }
    
    private function createMigrationsTable()
    {
        $query = "
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $this->conn->exec($query);
        } catch (\PDOException $e) {
            echo "Erro ao criar tabela de migrations: " . $e->getMessage();
            exit;
        }
    }
    
    public function runMigrations()
    {
        // Verificar migrations já executadas
        $executed = $this->getExecutedMigrations();
        $batch = $this->getNextBatch();
        $success = true;
        $count = 0;
        
        foreach ($this->migrations as $migration) {
            $className = basename(str_replace('\\', '/', $migration));
            
            if (!in_array($className, $executed)) {
                echo "Executando migration: $className\n";
                $instance = new $migration();
                
                if ($instance->up()) {
                    $this->addMigrationRecord($className, $batch);
                    $count++;
                } else {
                    $success = false;
                    echo "Falha ao executar migration: $className\n";
                    break;
                }
            }
        }
        
        if ($success) {
            echo "$count migrations executadas com sucesso.\n";
        }
        
        return $success;
    }
    
    public function rollback()
    {
        $lastBatch = $this->getLastBatch();
        
        if ($lastBatch === 0) {
            echo "Nenhuma migration para reverter.\n";
            return true;
        }
        
        $query = "SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$lastBatch]);
        $migrations = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $success = true;
        $count = 0;
        
        foreach ($migrations as $migration) {
            $className = "Api\\Migrations\\$migration";
            
            if (class_exists($className)) {
                echo "Revertendo migration: $migration\n";
                $instance = new $className();
                
                if ($instance->down()) {
                    $count++;
                } else {
                    $success = false;
                    echo "Falha ao reverter migration: $migration\n";
                    break;
                }
            }
        }
        
        if ($success && $count > 0) {
            $query = "DELETE FROM migrations WHERE batch = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$lastBatch]);
            echo "$count migrations revertidas com sucesso.\n";
        }
        
        return $success;
    }
    
    private function getExecutedMigrations()
    {
        $query = "SELECT migration FROM migrations";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    private function getNextBatch()
    {
        $query = "SELECT MAX(batch) FROM migrations";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $lastBatch = $stmt->fetchColumn();
        return $lastBatch ? $lastBatch + 1 : 1;
    }
    
    private function getLastBatch()
    {
        $query = "SELECT MAX(batch) FROM migrations";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $lastBatch = $stmt->fetchColumn();
        return $lastBatch ? $lastBatch : 0;
    }
    
    private function addMigrationRecord($migration, $batch)
    {
        $query = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$migration, $batch]);
    }
}