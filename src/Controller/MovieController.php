<?php

namespace App\Controller;

use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    private $tmdbApiService;

    public function __construct(TmdbApiService $tmdbApiService)
    {
        $this->tmdbApiService = $tmdbApiService;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $genres = $this->tmdbApiService->getGenres();

        // Vérifiez si les genres existent
        $genreId = $genres['genres'][0]['id'] ?? null; // Utilisez la fusion null pour éviter les erreurs

        // Récupérer le meilleur film
        $bestMovies = $genreId ? $this->tmdbApiService->getMoviesByGenre($genreId) : ['results' => []];
        $bestMovie = $bestMovies['results'][0] ?? null; // Assurez-vous qu'il y a un film disponible

        // Récupérer la bande-annonce du meilleur film
        $trailerUrl = null;
        if ($bestMovie) {
            $videos = $this->tmdbApiService->getMovieVideos($bestMovie['id']);
            foreach ($videos['results'] as $video) {
                if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
                    $trailerUrl = 'https://www.youtube.com/embed/' . $video['key'];
                    break; // On prend seulement la première bande-annonce
                }
            }
        }

        return $this->render('movie/index.html.twig', [
            'genres' => $genres['genres'],
            'bestMovie' => $bestMovie,
            'trailerUrl' => $trailerUrl,
        ]);
    }

    /**
     * @Route("/genre/{id}", name="genre_movies")
     */
    public function showMoviesByGenre($id): Response
    {
        $genres = $this->tmdbApiService->getGenres();
        
        // Récupérer les films du genre spécifié
        $movies = $this->tmdbApiService->getMoviesByGenre($id);

        // Vérifiez si les films existent
        foreach ($movies['results'] as &$movie) {
            $videos = $this->tmdbApiService->getMovieVideos($movie['id']);
            $movie['trailer_key'] = null; // Initialiser la clé de bande-annonce
            foreach ($videos['results'] as $video) {
                if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
                    $movie['trailer_key'] = $video['key']; // Assigner la clé de la bande-annonce
                    break; // On prend seulement la première bande-annonce
                }
            }
        }

        return $this->render('movie/movies.html.twig', [
            'genres' => $genres['genres'],
            'movies' => $movies['results'] ?? [], // Eviter les erreurs si aucune résultat
        ]);
    }

    /**
     * @Route("/search", name="search_movie")
     */
    public function searchMovie(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $results = $this->tmdbApiService->searchMovie($query);
        return new JsonResponse($results);
    }
}
