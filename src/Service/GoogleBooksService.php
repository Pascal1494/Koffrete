<?php

namespace App\Service;

use App\Dto\BookMetadataDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleBooksService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    public function fetchByIsbn(string $isbn): ?BookMetadataDto
    {
        // Clean ISBN input (remove hyphens, spaces)
        $cleanIsbn = str_replace(['-', ' '], '', $isbn);

        try {
            $response = $this->httpClient->request('GET', 'https://www.googleapis.com/books/v1/volumes', [
                'query' => [
                    'q' => 'isbn:' . $cleanIsbn,
                ],
            ]);

            $data = $response->toArray();

            if (($data['totalItems'] ?? 0) === 0 || !isset($data['items'][0])) {
                return null;
            }

            $volumeInfo = $data['items'][0]['volumeInfo'] ?? [];
            
            $title = $volumeInfo['title'] ?? 'Livre Inconnu';
            $authors = $volumeInfo['authors'] ?? [];
            $author = !empty($authors) ? implode(', ', $authors) : 'Auteur Inconnu';
            
            $publishedDate = $volumeInfo['publishedDate'] ?? null;
            $publishedYear = $publishedDate ? substr($publishedDate, 0, 4) : null;
            
            $description = $volumeInfo['description'] ?? null;
            $coverUrl = $volumeInfo['imageLinks']['thumbnail'] ?? null;

            return new BookMetadataDto(
                title: $title,
                author: $author,
                isbn: $cleanIsbn,
                publishedYear: $publishedYear,
                description: $description,
                coverUrl: $coverUrl
            );
        } catch (\Throwable) {
            return null; // Silent catch, returns null in case of API issues
        }
    }
}