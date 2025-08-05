<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Entities\Author;
use App\Repositories\AuthorRepository;

class AuthorController
{
    private AuthorRepository $authorRepository;

    public function __construct()
    {
        $this->authorRepository = new AuthorRepository();
    }

    public function authorToArray(Author $author): array
    {
        return [
            'id' => $author->getId(),
            'first_name' => $author->getFirstName(),
            'last_name' => $author->getLastName(),
            'username' => $author->getUsername(),
            'email' => $author->getEmail(),
            'orcid' => $author->getOrcid(),
            'affiliation' => $author->getAffiliation()
        ];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        // ------------------ GET ------------------
        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $author = $this->authorRepository->findById((int)$_GET['id']);
                echo json_encode($author ? $this->authorToArray($author) : null);
            } else {
                $list = array_map(
                    fn(Author $author) => $this->authorToArray($author),
                    $this->authorRepository->findAll()
                );
                echo json_encode($list);
            }
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        // ------------------ POST ------------------
        if ($method === 'POST') {
            if (empty($payload['first_name']) || empty($payload['last_name']) || empty($payload['email'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos obligatorios faltantes']);
                return;
            }

            // Validar que no exista autor con el mismo email
            $existing = $this->authorRepository->findByEmail($payload['email'] ?? '');
            if ($existing) {
                http_response_code(400);
                echo json_encode(['error' => 'El autor ya existe con este email']);
                return;
            }

            $author = new Author(
                0,
                $payload['first_name'],
                $payload['last_name'],
                $payload['username'] ?? '',
                $payload['email'],
                $payload['password'] ?? '',
                $payload['orcid'] ?? '',
                $payload['affiliation'] ?? ''
            );

            echo json_encode(['success' => $this->authorRepository->create($author)]);
            return;
        }

        // ------------------ PUT ------------------
        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->authorRepository->findById($id);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Autor no encontrado']);
                return;
            }

            if (isset($payload['first_name'])) $existing->setFirstName($payload['first_name']);
            if (isset($payload['last_name'])) $existing->setLastName($payload['last_name']);
            if (isset($payload['username'])) $existing->setUsername($payload['username']);
            if (isset($payload['email'])) $existing->setEmail($payload['email']);
            if (isset($payload['password'])) $existing->setPassword($payload['password']);
            if (isset($payload['orcid'])) $existing->setOrcid($payload['orcid']);
            if (isset($payload['affiliation'])) $existing->setAffiliation($payload['affiliation']);

            echo json_encode(['success' => $this->authorRepository->update($existing)]);
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

            echo json_encode(['success' => $this->authorRepository->delete($id)]);
            return;
        }

        // ------------------ OTROS MÉTODOS ------------------
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }
}
