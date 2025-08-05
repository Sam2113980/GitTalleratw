<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Author;
use PDO; //PHP DATA OBJECT
class AuthorRepository implements RepositoryInterface{

    //1. Establecer la conexion a la BD
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    //2. Implementar los metodos de la interfaz
     public function findAll(): array{
        $stmt = $this->db->query("SELECT * FROM author"); // salida del sql
        $list = [];
        while($row=$stmt->fetch()){
            $list[] = $this->hydrate($row); //row->fila sql
        }
        return $list;
     }

     public function findByID(int $id): ?object
     {
        $sql = "SELECT * FROM author WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null; // Si se encuentra el autor, retornar el objeto Author
     }

    public function create (object $entity): bool
     {
         if (!$entity instanceof Author) {
             throw new \InvalidArgumentException('Expected an instance of Author');
         }
         //No ingresar el id, ya que es autoincremental
         $sql = "INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation) 
                 VALUES (:fn, :ln, :usrn, :email, :paswd, :orcid, :aff)";

        $stmt = $this->db->prepare($sql);
     
        return $stmt->execute([
            ':fn' => $entity->getFirstName(),
            ':ln' => $entity->getLastName(),
            ':usrn' => $entity->getUsername(),
            ':email' => $entity->getEmail(),
            ':paswd' => $entity->getPassword(),
            ':orcid' => $entity->getOrcid(),
            ':aff' => $entity->getAffiliation()
        ]);
    }
    public function update(object $entity):bool{
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException('Expected an instance of Author');
        }
        $sql = "UPDATE author SET first_name = :fn, last_name = :ln, username = :usrn, email = :email, 
                password = :paswd, orcid = :orcid, affiliation = :aff WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $entity->getId(),
            ':fn' => $entity->getFirstName(),
            ':ln' => $entity->getLastName(),
            ':usrn' => $entity->getUsername(),
            ':email' => $entity->getEmail(),
            ':paswd' => $entity->getPassword(),
            ':orcid' => $entity->getOrcid(),
            ':aff' => $entity->getAffiliation()
        ]);

    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM author WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    //Convierte una fila de la base de datos a un objeto Author
    private function hydrate(array $row): Author
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
        return $author;
    }

    //Replicar lo mismo en articulo y revista
}