<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php'; // si usas Composer y PSR-4

use App\Entities\Article;
use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;
use App\Repositories\ArticleRepository;

// Instancia del repositorio
$repository = new BookRepository();
$repositoryAr = new ArticleRepository();
$repoitoryAut = new AuthorRepository();

// Probar findAll()
$books = $repository->findAll();
$articles = $repositoryAr->findAll();
$autors = $repoitoryAut->findALL();

echo "LISTA DE LIBROS \n";
foreach ($books as $book) {
    echo "Publication ID:".$book->getId().PHP_EOL;
    echo "Título: " . $book->getTitle() . PHP_EOL;
    echo "Autor: " . $book->getAuthor()->getFirstName() . PHP_EOL;
    echo "ISBN: " . $book->getIsbn() . PHP_EOL;
    echo "-----" . PHP_EOL;
}

 echo "LISTA DE ARTICULOS \n";
foreach ($articles as $article) {
    echo "Publication ID:".$article->getId().PHP_EOL;
    echo "Título: " . $article->getTitle() . PHP_EOL;
    echo "Autor: " . $article->getAuthor()->getFirstName(). PHP_EOL;
    echo "DOI: " . $article->getDoi() . PHP_EOL;
    echo "-----" . PHP_EOL;
}

 echo "LISTA DE AUTORES \n";
foreach ($autors as $autor) {
    echo "Autor ID:".$autor->getId().PHP_EOL;
    echo "Nombre: " .$autor->getFirstName() . PHP_EOL;
    echo "Apellido: " .$autor->getLastName() . PHP_EOL;
    echo "Password: " .$autor->getPassword() . PHP_EOL;
    echo "Orcid: " . $autor->getOrcid() . PHP_EOL;
    echo "-----" . PHP_EOL;
}

