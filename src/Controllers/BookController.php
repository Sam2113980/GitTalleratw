<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Book;
use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;

class BookController
{
    private BookRepository $bookRepository;
    private AuthorRepository $authorRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
        $this->authorRepository = new AuthorRepository();
    }

    public function bookToArray(Book $book): array
    {
        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'first_name' => $book->getAuthor()->getFirstName(),
                'last_name' => $book->getAuthor()->getLastName()
            ],
            'isbn' => $book->getIsbn(),
            'genre' => $book->getGenre(),
            'edition' => $book->getEdition()
        ];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        // ------------------ GET ------------------
        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $book = $this->bookRepository->findById((int)$_GET['id']);
                echo json_encode($book ? $this->bookToArray($book) : null);
            } else {
                $list = array_map(
                    fn(Book $book) => $this->bookToArray($book),
                    $this->bookRepository->findAll()
                );
                echo json_encode($list);
            }
            return;
        }

        $payLoad = json_decode(file_get_contents('php://input'), true);

        // ------------------ POST ------------------
        if ($method === 'POST') {
            if (empty($payLoad['title']) || empty($payLoad['isbn'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Título e ISBN son obligatorios']);
                return;
            }

            $author = $this->authorRepository->findById((int)($payLoad['author'] ?? 0));
            if (!$author) {
                http_response_code(400);
                echo json_encode(['error' => 'Autor no encontrado']);
                return;
            }

            $book = new Book(
                0,
                $payLoad['title'],
                $payLoad['description'] ?? '',
                new \DateTime($payLoad['publication_date'] ?? 'now'),
                $author,
                $payLoad['isbn'],
                $payLoad['genre'] ?? '',
                $payLoad['edition'] ?? ''
            );

            echo json_encode(['success' => $this->bookRepository->create($book)]);
            return;
        }

        // ------------------ PUT ------------------
        if ($method === 'PUT') {
            $id = (int)($payLoad['id'] ?? 0);
            $existing = $this->bookRepository->findById($id);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Libro no encontrado']);
                return;
            }

            if (isset($payLoad['author'])) {
                $author = $this->authorRepository->findById((int)$payLoad['author']);
                if ($author) {
                    $existing->setAuthor($author);
                }
            }

            if (isset($payLoad['title'])) $existing->setTitle($payLoad['title']);
            if (isset($payLoad['description'])) $existing->setDescription($payLoad['description']);
            if (isset($payLoad['publication_date'])) {
                $existing->setPublicationDate(new \DateTime($payLoad['publication_date']));
            }
            if (isset($payLoad['isbn'])) $existing->setIsbn($payLoad['isbn']);
            if (isset($payLoad['genre'])) $existing->setGenre($payLoad['genre']);
            if (isset($payLoad['edition'])) $existing->setEdition($payLoad['edition']);

            echo json_encode(['success' => $this->bookRepository->update($existing)]);
            return;
        }

        // ------------------ DELETE ------------------
        if ($method === 'DELETE') {
            $id = (int)($payLoad['id'] ?? 0);
            if ($id === 0) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido para eliminar']);
                return;
            }

            echo json_encode(['success' => $this->bookRepository->delete($id)]);
            return;
        }

        // ------------------ OTROS MÉTODOS ------------------
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }
}
