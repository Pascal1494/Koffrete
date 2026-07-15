<?php

namespace App\Controller\Api;

use App\Service\GoogleBooksService;
use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/metadata')]
#[IsGranted('ROLE_USER')]
class MetadataController extends AbstractController
{
    #[Route('/book/{isbn}', name: 'api_metadata_book', methods: ['GET'])]
    public function getBookMetadata(string $isbn, GoogleBooksService $booksService): JsonResponse
    {
        $dto = $booksService->fetchByIsbn($isbn);

        if (!$dto) {
            return $this->json(['error' => 'Livre introuvable pour cet ISBN'], 404);
        }

        return $this->json($dto);
    }

    #[Route('/movie/search', name: 'api_metadata_movie_search', methods: ['GET'])]
    public function searchMovie(Request $request, TmdbService $tmdbService): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (empty($query)) {
            return $this->json(['error' => 'Veuillez saisir un titre'], 400);
        }

        $results = $tmdbService->searchByTitle($query);

        return $this->json($results);
    }

    #[Route('/movie/details/{id}', name: 'api_metadata_movie_details', methods: ['GET'])]
    public function movieDetails(int $id, TmdbService $tmdbService): JsonResponse
    {
        $dto = $tmdbService->fetchDetails($id);

        if (!$dto) {
            return $this->json(['error' => 'Détails du film introuvables'], 404);
        }

        return $this->json($dto);
    }
}