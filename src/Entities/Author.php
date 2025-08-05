<?php
declare (strict_types=1);
namespace App\Entities;

class Author{

   private int  $id;
   private string  $first_name; 
   private string  $last_name; 
   private string  $username; 
   private string  $email; 
   private string  $password; 
   private string  $orcid;
   private string  $affiliation; 

    public function __construct(
        int $id, 
        string $first_name,
        string $last_name, 
        string $username, 
        string $email, 
        string $password, 
        string $orcid, 
        string $affiliation
     ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->orcid = $orcid;
        $this->affiliation = $affiliation;
     }

     //Completar Getters y Setters
    public function getId(): int {
        return $this->id;
    }
      public function getFirstName(): string {
         return $this->first_name;
      }
      public function getLastName(): string {
         return $this->last_name;
      }  
      public function getUsername(): string {
         return $this->username;
      }
      public function getEmail(): string {
         return $this->email;
      }
      public function getPassword(): string {
         return $this->password;
      }
      public function getOrcid(): string {
         return $this->orcid;
      }
      public function getAffiliation(): string {
         return $this->affiliation;
      }
      public function setId(int $id): void {
         $this->id = $id;
      }
      public function setFirstName(string $first_name): void {
         $this->first_name = $first_name;
      }
      public function setLastName(string $last_name): void {
         $this->last_name = $last_name;
      }
      public function setUsername(string $username): void {
         $this->username = $username;
      }
      public function setEmail(string $email): void {
         $this->email = $email;
      }
      public function setPassword(string $plain): void {
         //$this->password = $password;
         $this->password = password_hash($plain, PASSWORD_BCRYPT); // Hashing the password for security
      }
      public function setOrcid(string $orcid): void {
         $this->orcid = $orcid;
      }
      public function setAffiliation(string $affiliation): void {
         $this->affiliation = $affiliation;
      }

}
