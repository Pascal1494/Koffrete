<?php

namespace App\Entity;

use App\Repository\DvdRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DvdRepository::class)]
class Dvd extends Media
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $director = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $actors = [];

    #[ORM\Column(nullable: true)]
    private ?int $durationInMinutes = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $releaseYear = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $posterUrl = null;

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(?string $director): static
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getActors(): ?array
    {
        return $this->actors;
    }

    /**
     * @param array<string>|null $actors
     */
    public function setActors(?array $actors): static
    {
        $this->actors = $actors;

        return $this;
    }

    public function getDurationInMinutes(): ?int
    {
        return $this->durationInMinutes;
    }

    public function setDurationInMinutes(?int $durationInMinutes): static
    {
        $this->durationInMinutes = $durationInMinutes;

        return $this;
    }

    public function getReleaseYear(): ?string
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?string $releaseYear): static
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getPosterUrl(): ?string
    {
        return $this->posterUrl;
    }

    public function setPosterUrl(?string $posterUrl): static
    {
        $this->posterUrl = $posterUrl;

        return $this;
    }
}