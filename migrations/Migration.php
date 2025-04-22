<?php
namespace Api\Migrations;

use Api\Config\Database;

abstract class Migration
{
    protected $conn;
    
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Método que deve ser implementado por todas as migrations
    abstract public function up();
    
    // Método para reverter as alterações (opcional)
    abstract public function down();
}