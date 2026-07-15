<?php

namespace App\Dto;

class MovieMetadataDto
{
    /**
     * @param string[] $actors
     */
    public function __construct(
        public string $externalId,
        public string $title,
        public ?int $durationInMinutes,
        public ?string $releaseYear,
        public array $actors,
        public ?string $director,
        public ?string $posterUrl,
        public ?string $synopsis
    ) {}
}