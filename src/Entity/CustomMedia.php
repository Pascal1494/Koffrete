<?php

namespace App\Entity;

use App\Repository\CustomMediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomMediaRepository::class)]
class CustomMedia extends Media
{
    #[ORM\Column(length: 100)]
    private ?string $type = null; // e.g. "CD", "Vinyle", "Jeu de société", "Jeu vidéo", "K7 Audio"

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $attributes = [];

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, mixed>|null $attributes
     */
    public function setAttributes(?array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }
}