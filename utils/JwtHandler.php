<?php
namespace Api\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler {
    private $secretKey = "sua_chave_secreta_muito_segura"; // Em produção, use uma variável de ambiente
    private $issuer = "php_api_jwt";
    private $audience = "api_users";
    private $issuedAt;
    private $expiry;
    
    public function __construct() {
        $this->issuedAt = time();
        // Token válido por 1 hora
        $this->expiry = $this->issuedAt + 3600;
    }
    
    public function generateToken($userId) {
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $this->issuedAt,
            'exp' => $this->expiry,
            'data' => [
                'user_id' => $userId
            ]
        ];
        
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
    
    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            throw new \Exception("Token inválido: " . $e->getMessage(), 401);
        }
    }
}