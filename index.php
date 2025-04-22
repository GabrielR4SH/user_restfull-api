<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/vendor/autoload.php';

// Obter e processar o URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Remover segmentos vazios e o nome do subdiretório (user_restfull-api)
$uri = array_filter($uri);
$uri = array_values($uri); // Reindexar array

// Se o primeiro segmento é o nome do subdiretório, removê-lo
if (isset($uri[0]) && $uri[0] == 'user_restfull-api') {
    array_shift($uri);
}

// Endpoints da API
$routes = [
    'auth' => [
        'register' => ['POST' => 'Api\Controllers\AuthController@register'],
        'login' => ['POST' => 'Api\Controllers\AuthController@login']
    ],
    'users' => [
        '' => [
            'GET' => 'Api\Controllers\UserController@index',
            'POST' => 'Api\Controllers\UserController@create'
        ],
        '{id}' => [
            'GET' => 'Api\Controllers\UserController@show',
            'PUT' => 'Api\Controllers\UserController@update',
            'DELETE' => 'Api\Controllers\UserController@delete'
        ]
    ]
];

// Definir variáveis para o processamento de rota
$resourceName = isset($uri[0]) ? $uri[0] : '';
$resourceId = isset($uri[1]) ? $uri[1] : '';
$subResource = isset($uri[2]) ? $uri[2] : '';

$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    // Roteamento simples
    if (isset($routes[$resourceName])) {
        $resource = $routes[$resourceName];
        
        if ($resourceId && isset($resource['{id}']) && isset($resource['{id}'][$requestMethod])) {
            $handler = $resource['{id}'][$requestMethod];
        } elseif ($resourceId && isset($resource[$resourceId]) && isset($resource[$resourceId][$requestMethod])) {
            $handler = $resource[$resourceId][$requestMethod];
        } elseif (isset($resource[''][$requestMethod])) {
            $handler = $resource[''][$requestMethod];
        } else {
            throw new Exception("Rota não encontrada", 404);
        }
        
        // Parse controller@method
        list($controller, $method) = explode('@', $handler);
        $controllerInstance = new $controller();
        
        // Se não é autenticação, verifica token JWT
        if ($resourceName !== 'auth') {
            $middleware = new Api\Middleware\AuthMiddleware();
            $middleware->handle();
        }
        
        // Executa o controlador
        $response = $controllerInstance->$method($resourceId);
        echo json_encode($response);
        
    } else {
        throw new Exception("Recurso não encontrado", 404);
    }
} catch (Exception $e) {
    header("HTTP/1.1 " . ($e->getCode() ? $e->getCode() : 500));
    echo json_encode(['error' => $e->getMessage()]);
}