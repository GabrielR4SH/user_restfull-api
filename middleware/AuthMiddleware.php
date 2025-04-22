<?php
namespace Api\Middleware;

use Api\Utils\JwtHandler;

class AuthMiddleware {
    public function handle() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            throw new \Exception("Token de acesso não fornecido", 401);
        }
        
        $authHeader = $headers['Authorization'];
        
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $jwtHandler = new JwtHandler();
            
            try {
                $userData = $jwtHandler->validateToken($token);
                // Define o usuário autenticado para uso posterior
                $_SERVER['auth_user'] = $userData;
                return true;
            } catch (\Exception $e) {
                throw new \Exception("Não autorizado: " . $e->getMessage(), 401);
            }
        }
        
        throw new \Exception("Token de formato inválido", 401);
    }
}