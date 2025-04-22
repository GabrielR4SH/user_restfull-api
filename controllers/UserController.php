<?php
namespace Api\Controllers;

use Api\Models\User;

class UserController {
    
    // Listar todos os usuários
    public function index() {
        $user = new User();
        $users = $user->getAll();
        
        return [
            'status' => 1,
            'users' => $users
        ];
    }
    
    // Obter um usuário por ID
    public function show($id) {
        $user = new User();
        $userData = $user->getById($id);
        
        if ($userData) {
            return [
                'status' => 1,
                'user' => $userData
            ];
        }
        
        return [
            'status' => 0,
            'message' => 'Usuário não encontrado'
        ];
    }
    
    // Criar um novo usuário (admin)
    public function create() {
        // Verificar se o usuário logado tem permissão (exemplo: admin)
        $authUser = $_SERVER['auth_user'];
        
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
            return [
                'status' => 1,
                'message' => 'Usuário criado com sucesso',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];
        }
        
        return [
            'status' => 0,
            'message' => 'Falha ao criar usuário'
        ];
    }
    
    // Atualizar um usuário
    public function update($id) {
        // Verificar se o usuário logado é o mesmo da solicitação ou é admin
        $authUser = $_SERVER['auth_user'];
        $userId = $authUser->user_id;
        
        if ($userId != $id) {
            return [
                'status' => 0,
                'message' => 'Você não tem permissão para atualizar este usuário'
            ];
        }
        
        // Receber dados do formulário
        $data = json_decode(file_get_contents("php://input"));
        
        $user = new User();
        $userData = $user->getById($id);
        
        if (!$userData) {
            return [
                'status' => 0,
                'message' => 'Usuário não encontrado'
            ];
        }
        
        // Atualizar campos
        $user->id = $id;
        $user->name = isset($data->name) ? $data->name : $userData['name'];
        $user->email = isset($data->email) ? $data->email : $userData['email'];
        
        if ($user->update()) {
            return [
                'status' => 1,
                'message' => 'Usuário atualizado com sucesso',
                'user' => [
                    'id' => $id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];
        }
        
        return [
            'status' => 0,
            'message' => 'Falha ao atualizar usuário'
        ];
    }
    
    // Excluir um usuário
    public function delete($id) {
        // Verificar se o usuário logado é o mesmo da solicitação ou é admin
        $authUser = $_SERVER['auth_user'];
        $userId = $authUser->user_id;
        
        if ($userId != $id) {
            return [
                'status' => 0,
                'message' => 'Você não tem permissão para excluir este usuário'
            ];
        }
        
        $user = new User();
        
        if ($user->delete($id)) {
            return [
                'status' => 1,
                'message' => 'Usuário excluído com sucesso'
            ];
        }
        
        return [
            'status' => 0,
            'message' => 'Falha ao excluir usuário'
        ];
    }
}