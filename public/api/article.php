<?php
// De este archivo llamamos a las operaciones del controlador de libros
require __DIR__ .'/..//../vendor/autoload.php';

use App\Controllers\ArticleController;

(new ArticleController())->handle();