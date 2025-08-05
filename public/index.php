<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\BookController;
use App\Controllers\AuthorController;
use App\Controllers\ArticleController;

// Obtener la ruta solicitada (ejemplo: /api/book)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

// ------------------ RUTAS API ------------------

// Libros
if ($uri === '/api/book') {
    (new BookController())->handle();
    exit;
}

// Autores
if ($uri === '/api/author') {
    (new AuthorController())->handle();
    exit;
}

// Artículos
if ($uri === '/api/article') {
    (new ArticleController())->handle();
    exit;
}

// ------------------ RUTA NO ENCONTRADA ------------------
http_response_code(404);
echo json_encode(['error' => 'Ruta no encontrada']);
