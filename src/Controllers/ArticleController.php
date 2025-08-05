<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Entities\Author;
use App\Entities\Article;
use App\Repositories\AuthorRepository;
use App\Repositories\ArticleRepository;

class ArticleController
{
    private ArticleRepository $articleRepository;
    private AuthorRepository $authorRepository;

    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
        $this->authorRepository = new AuthorRepository();
    }

    public function articleToArray(Article $article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'publication_date' => $article->getPublicationDate()->format('Y-m-d'),
            'author' => [
                'id' => $article->getAuthor()->getId(),
                'first_name' => $article->getAuthor()->getFirstName(),
                'last_name' => $article->getAuthor()->getLastName(),
            ],
            'doi' => $article->getDoi(),
            'abstract' => $article->getAbstract(),
            'keywords' => $article->getKeywords(),
            'indexacion' => $article->getIndexacion(),
            'magazine' => $article->getMagazine(),
            'area' => $article->getArea()
        ];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        // ------------------ GET ------------------
        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $article = $this->articleRepository->findById((int)$_GET['id']);
                echo json_encode($article ? $this->articleToArray($article) : null);
            } else {
                $list = array_map(
                    fn(Article $article) => $this->articleToArray($article),
                    $this->articleRepository->findAll()
                );
                echo json_encode($list);
            }
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        // ------------------ POST ------------------
        if ($method === 'POST') {
            if (empty($payload['title']) || empty($payload['doi'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Título y DOI son obligatorios']);
                return;
            }

            $author = $this->authorRepository->findById((int)($payload['author'] ?? 0));
            if (!$author) {
                http_response_code(400);
                echo json_encode(['error' => 'Autor no encontrado']);
                return;
            }

            $article = new Article(
                0,
                $payload['title'],
                $payload['description'] ?? '',
                new \DateTime($payload['publication_date'] ?? 'now'),
                $author,
                $payload['doi'],
                $payload['abstract'] ?? '',
                $payload['keywords'] ?? '',
                $payload['indexacion'] ?? '',
                $payload['magazine'] ?? '',
                $payload['area'] ?? ''
            );

            echo json_encode(['success' => $this->articleRepository->create($article)]);
            return;
        }

        // ------------------ PUT ------------------
        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->articleRepository->findById($id);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Artículo no encontrado']);
                return;
            }

            if (isset($payload['author'])) {
                $author = $this->authorRepository->findById((int)$payload['author']);
                if ($author) $existing->setAuthor($author);
            }

            if (isset($payload['title'])) $existing->setTitle($payload['title']);
            if (isset($payload['description'])) $existing->setDescription($payload['description']);
            if (isset($payload['publication_date'])) {
                $existing->setPublicationDate(new \DateTime($payload['publication_date']));
            }
            if (isset($payload['doi'])) $existing->setDoi($payload['doi']);
            if (isset($payload['abstract'])) $existing->setAbstract($payload['abstract']);
            if (isset($payload['keywords'])) $existing->setKeywords($payload['keywords']);
            if (isset($payload['indexacion'])) $existing->setIndexacion($payload['indexacion']);
            if (isset($payload['magazine'])) $existing->setMagazine($payload['magazine']);
            if (isset($payload['area'])) $existing->setArea($payload['area']);

            echo json_encode(['success' => $this->articleRepository->update($existing)]);
            return;
        }

        // ------------------ DELETE ------------------
        if ($method === 'DELETE') {
            $id = (int)($payload['id'] ?? 0);
            if ($id === 0) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido para eliminar']);
                return;
            }

            echo json_encode(['success' => $this->articleRepository->delete($id)]);
            return;
        }

        // ------------------ OTROS MÉTODOS ------------------
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }
}
