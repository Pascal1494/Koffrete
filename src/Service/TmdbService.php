<?php

namespace App\Service;

use App\Dto\MovieMetadataDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $tmdbApiKey
    ) {}

    /**
     * Search movies by title.
     *
     * @return array<array{id: int, title: string, release_date: string, poster_path: ?string}>
     */
    public function searchByTitle(string $title): array
    {
        if (empty($this->tmdbApiKey) || $this->tmdbApiKey === 'placeholder') {
            // Graceful fallback for local dev without key
            return [
                [
                    'id' => 27205,
                    'title' => 'Inception (Simulé)',
                    'release_date' => '2010-07-15',
                    'poster_path' => null
                ]
            ];
        }

        try {
            $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/search/movie', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->tmdbApiKey,
                    'accept' => 'application/json',
                ],
                'query' => [
                    'query' => $title,
                    'language' => 'fr-FR',
                ],
            ]);

            $data = $response->toArray();
            return $data['results'] ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Fetch complete movie details including cast and crew.
     */
    public function fetchDetails(int $movieId): ?MovieMetadataDto
    {
        if (empty($this->tmdbApiKey) || $this->tmdbApiKey === 'placeholder') {
            // Graceful mock fallback
            return new MovieMetadataDto(
                externalId: (string) $movieId,
                title: 'Inception (Simulé)',
                durationInMinutes: 148,
                releaseYear: '2010',
                actors: ['Leonardo DiCaprio', 'Joseph Gordon-Levitt', 'Marion Cotillard'],
                director: 'Christopher Nolan',
                posterUrl: null,
                synopsis: 'Un voleur expérimenté est chargé de l\'ultime mission : implanter une idée dans l\'esprit d\'un individu.'
            );
        }

        try {
            // 1. Fetch main details
            $detailsResponse = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/movie/' . $movieId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->tmdbApiKey,
                    'accept' => 'application/json',
                ],
                'query' => [
                    'language' => 'fr-FR',
                ],
            ]);
            $details = $detailsResponse->toArray();

            // 2. Fetch credits (directors & actors)
            $creditsResponse = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/movie/' . $movieId . '/credits', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->tmdbApiKey,
                    'accept' => 'application/json',
                ],
                'query' => [
                    'language' => 'fr-FR',
                ],
            ]);
            $credits = $creditsResponse->toArray();

            // Find Director
            $director = null;
            foreach ($credits['crew'] ?? [] as $crewMember) {
                if (($crewMember['job'] ?? '') === 'Director') {
                    $director = $crewMember['name'];
                    break;
                }
            }

            // Extract primary actors (top 5)
            $actors = [];
            $cast = $credits['cast'] ?? [];
            for ($i = 0; $i < min(5, count($cast)); $i++) {
                $actors[] = $cast[$i]['name'];
            }

            $title = $details['title'] ?? 'Film Inconnu';
            $duration = $details['runtime'] ?? null;
            $releaseDate = $details['release_date'] ?? null;
            $releaseYear = $releaseDate ? substr($releaseDate, 0, 4) : null;
            $synopsis = $details['overview'] ?? null;
            
            $posterPath = $details['poster_path'] ?? null;
            $posterUrl = $posterPath ? 'https://image.tmdb.org/t/p/w500' . $posterPath : null;

            return new MovieMetadataDto(
                externalId: (string) $movieId,
                title: $title,
                durationInMinutes: $duration,
                releaseYear: $releaseYear,
                actors: $actors,
                director: $director,
                posterUrl: $posterUrl,
                synopsis: $synopsis
            );
        } catch (\Throwable) {
            return null;
        }
    }
}