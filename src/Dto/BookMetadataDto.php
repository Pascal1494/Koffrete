<?php

namespace App\Dto;

class BookMetadataDto
{
    public function __construct(
        public string $title,
        public ?string $author,
        public ?string $isbn,
        public ?string $publishedYear,
        public ?string $description,
        public ?string $coverUrl
    ) {}
}