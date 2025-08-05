<?php

declare(strict_types=1);

namespace App\Entities;

class Article extends Publication
{
    private string $doi;
    private string $abstract;
    private string $keywords;
    private string $indexacion;
    private string $magazine;
    private string $area;

    public function __construct(
        int $id,
        string $title,
        string $description,
        \DateTime $publication_date,
        Author $author,
        string $doi,
        string $abstract,
        string $keywords,
        string $indexacion,
        string $magazine,
        string $area
    ) {
        parent::__construct($id, $title, $description, $publication_date, $author);
        $this->doi = $doi;
        $this->abstract = $abstract;
        $this->keywords = $keywords;
        $this->indexacion = $indexacion;
        $this->magazine = $magazine;
        $this->area = $area;
    }

    // Getters
    public function getDoi(): string
    {
        return $this->doi;
    }
    public function getAbstract(): string
    {
        return $this->abstract;
    }
    public function getKeywords(): string
    {
        return $this->keywords;
    }
    public function getIndexacion(): string
    {
        return $this->indexacion;
    }
    public function getMagazine(): string
    {
        return $this->magazine;
    }
    public function getArea(): string
    {
        return $this->area;
    }
    public function getType(): string
    {
        return 'article';
    }
    // Setters
    public function setDoi(string $doi): void
    {
        $this->doi = $doi;
    }
    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }
    public function setIndexacion(string $indexacion): void
    {
        $this->indexacion = $indexacion;
    }
    public function setMagazine(string $magazine): void
    {
        $this->magazine = $magazine;
    }
    public function setArea(string $area): void
    {
        $this->area = $area;
    }
}
