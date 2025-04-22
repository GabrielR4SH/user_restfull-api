<?php
namespace Api\Controllers;

use Api\Models\User;
use Api\Utils\JwtHandler;

class AuthController {
    
    public function register() {
        // Receber dados do formulário
        $data = json_decode(file_get_contents("php://input"));
        
        // Verificar campos obrigatórios
        if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
            return [
                'status' => 0,
                'message' => 'Campos obrigatórios faltando'
            ];
        }
        
        $user = new User();
        
        // Verificar se email já existe
        if ($user->getByEmail($data->email)) {
            return [
                'status' => 0,
                'message' => 'Email já está em uso'
            ];
        }
        
        // Criar novo usuário
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = $data->password;
        
        if ($user->create()) {
            // Gerar token JWT
            $jwtHandler = new JwtHandler();
            $token = $jwtHandler->generateToken($user->id);
            
            return [
                'status' => 1,
                'message' => 'Usuário registrado com sucesso',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];
        }
        
        return [
            'status' => 0,
            'message' => 'Falha ao registrar usuário'
        ];
    }
    
    public function login() {
        // Receber dados do login
        $data = json_decode(file_get_contents("php://input"));
        
        // Verificar campos obrigatórios
        if (!isset($data->email) || !isset($data->password)) {
            return [
                'status' => 0,
                'message' => 'Email e senha são obrigatórios'
            ];
        }
        
        $user = new User();
        $userData = $user->getByEmail($data->email);
        
        // Verificar se o usuário existe
        if (!$userData) {
            return [
                'status' => 0,
                'message' => 'Email ou senha incorretos'
            ];
        }
        
        // Verificar senha
        if (!password_verify($data->password, $userData['password'])) {
            return [
                'status' => 0,
                'message' => 'Email ou senha incorretos'
            ];
        }
        
        // Gerar token JWT
        $jwtHandler = new JwtHandler();
        $token = $jwtHandler->generateToken($userData['id']);
        
        return [
            'status' => 1,
            'message' => 'Login realizado com sucesso',
            'token' => $token,
            'user' => [
                'id' => $userData['id'],
                'name' => $userData['name'],
                'email' => $userData['email']
            ]
        ];
    }
}