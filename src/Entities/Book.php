<?php
declare(strict_types=1);
namespace App\Entities;

class Book extends Publication {
   private string $isbn ;
   private int $edition;
   private string $gender;
   
   //Constructor
    public function __construct(
          int $id,
          string $title,
          string $description,
          \DateTime $publication_date,
          Author $author,
          string $isbn,
          string $gender,
          int $edition,
     ) {
          parent::__construct($id, $title, $description, $publication_date, $author);
          $this->isbn = $isbn;
          $this->edition = $edition;
          $this->gender = $gender;
     }
    //Getters
     
    public function getIsbn(): string { return $this->isbn; }
    public function getEdition(): int { return $this->edition; }
    public function getGender(): string { return $this->gender; }
    //Setters
    
    public function setIsbn(string $isbn): void { $this->isbn = $isbn; }
    public function setEdition(int $edition): void { $this->edition = $edition; }
    public function setGender(string $gender): void { $this->gender = $gender; }
}
