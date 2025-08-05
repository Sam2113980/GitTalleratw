<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Repositories\AuthorRepository;
use App\Config\Database;
use App\Entities\Author;
use App\Entities\Article;
USE PDO; // PHP Data Object

class ArticleRepository implements RepositoryInterface
{
    // 1. Establecer la conexion a la BD
    private PDO $db;
    private AuthorRepository $authorRepository;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->authorRepository = new AuthorRepository();
    }
    // 2. Implementar los metodos de la interfaz
    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_article_list()");
        $rows = $stmt->fetchAll();
        $stmt->closeCursor(); // Cerrar el cursor para liberar la conexión
        $out = [];
        foreach ($rows as $r) {
            $out[] = $this->hydrate($r);
        }
        return $out;
    }

    public function findByID(int $id): ?object
    {
        $stmt = $this->db->prepare("CALL sp_find_article(:id)");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor(); // Cerrar el cursor para liberar la conexión
        return $row ? $this->hydrate($row) : null; // Si se encuentra el libro, retornar el objeto Book
    }

    public function create(object $entity):bool
    {
        
         if (!$entity instanceof Article) {
            throw new \InvalidArgumentException('Expected an instance of Article');
        }
        $stmt = $this->db->prepare("CALL sp_create_article(:title, :description, :publication_date, :author_id, :doi, :abstract, :keywords,:indexacion,:magazine,:area)");
        $ok = $stmt->execute([
            ':title' => $entity->getTitle(),
            ':description' => $entity->getDescription(),
            ':publication_date' => $entity->getPublicationDate()->format('Y-m-d'),
            ':author_id' => $entity->getAuthor()->getId(),
            ':doi' => $entity->getDoi(),
            ':abstract' => $entity->getAbstract(),
            ':keywords' => $entity->getKeywords(),
            ':indexacion' => $entity->getIndexacion(),
            ':magazine' => $entity->getMagazine(),
            ':area' => $entity->getArea()

        ]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor(); // Cerrar el cursor para liberar la conexión

        return $ok;
    }
    
    public function update(object $entity): bool
    {
        if (!$entity instanceof Article) {
            throw new \InvalidArgumentException('Expected an instance of Article');
        }
         $stmt = $this->db->prepare("CALL sp_update_article(:title, :description, :publication_date, :author_id, :doi, :abstract, :keywords,:indexacion,:magazine,:area)");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':title' => $entity->getTitle(),
            ':description' => $entity->getDescription(),
            ':publication_date' => $entity->getPublicationDate()->format('Y-m-d'),
            ':author_id' => $entity->getAuthor()->getId(),
            ':doi' => $entity->getDoi(),
            ':abstract' => $entity->getAbstract(),
            ':keywords' => $entity->getKeywords(),
            ':indexacion' => $entity->getIndexacion(),
            ':magazine' => $entity->getMagazine(),
            ':area' => $entity->getArea()
        ]);
        if ($ok) {
         $stmt->fetch();
        }
        $stmt->closeCursor(); // Cerrar el cursor para liberar la conexión

        return $ok;

    }
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_article(:id)");
        $ok = $stmt->execute([
            ':id' => $id
        ]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor(); // Cerrar el cursor para liberar la conexión

        return $ok;
    }
  

    /*
    public function hydrate(array $row): Article
    {
        $author = new Author(
            (int)$row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal',
            $row['orcid'],
            $row['affiliation']
        );
         //Reemplzar el HASH sin volver regenerar
        $ref = new \ReflectionClass($author);
        $prop = $ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $row['password']); // Set the hashed password
        return new Article(
            (int)$row['pulbication_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['doi'],
            $row['abstract'],
            $row['keywords'],
            $row['indexacion'],
            $row['magazine'],
            $row['area']
        );

    }
 */
   private function hydrate(array $row): Article
    {
        $author = new Author(
            (int)$row['author_id'],
            $row['first_name'],
            $row['last_name'],
            '',            
            '',            
            'temporal',    
            '',            
            ''             
        );

        // No es necesario usar Reflection ni setear password porque usamos valor fijo 'temporal'

        return new Article(
            (int)$row['publication_id'],
            $row['title'],
            $row['description'] ?? '',
            new \DateTime($row['publication_date']),
            $author,
            $row['doi'],
            $row['abstract'],
            $row['keywords'],
            $row['indexacion'],
            $row['magazine'],
            $row['area']
        );
    }


  
}