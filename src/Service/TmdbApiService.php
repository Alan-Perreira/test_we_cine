<?php


namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class TmdbApiService
{
    private $httpClient;
    private $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $tmdbApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $tmdbApiKey;
    }

    public function getGenres(): array
    {
        $url = 'https://api.themoviedb.org/3/genre/movie/list?api_key=' . $this->apiKey;

        try {
            $response = $this->httpClient->request('GET', $url);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \Exception("Erreur de transport lors de l'appel à l'API : " . $e->getMessage());
        } catch (ClientExceptionInterface $e) {
            throw new \Exception("Erreur client lors de l'appel à l'API : " . $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            throw new \Exception("Erreur serveur lors de l'appel à l'API : " . $e->getMessage());
        }
    }

    public function getMoviesByGenre(int $genreId): array
    {
        $url = 'https://api.themoviedb.org/3/discover/movie?api_key=' . $this->apiKey . '&with_genres=' . $genreId;

        try {
            $response = $this->httpClient->request('GET', $url);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \Exception("Erreur de transport lors de l'appel à l'API : " . $e->getMessage());
        } catch (ClientExceptionInterface $e) {
            throw new \Exception("Erreur client lors de l'appel à l'API : " . $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            throw new \Exception("Erreur serveur lors de l'appel à l'API : " . $e->getMessage());
        }
    }

    public function searchMovie(string $query): array
    {
        $url = 'https://api.themoviedb.org/3/search/movie?api_key=' . $this->apiKey . '&query=' . urlencode($query);

        try {
            $response = $this->httpClient->request('GET', $url);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \Exception("Erreur de transport lors de l'appel à l'API : " . $e->getMessage());
        } catch (ClientExceptionInterface $e) {
            throw new \Exception("Erreur client lors de l'appel à l'API : " . $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            throw new \Exception("Erreur serveur lors de l'appel à l'API : " . $e->getMessage());
        }
    }

    // Nouvelle méthode pour récupérer les vidéos d'un film
    public function getMovieVideos(int $movieId): array
    {
        $url = 'https://api.themoviedb.org/3/movie/' . $movieId . '/videos?api_key=' . $this->apiKey;

        try {
            $response = $this->httpClient->request('GET', $url);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \Exception("Erreur de transport lors de l'appel à l'API : " . $e->getMessage());
        } catch (ClientExceptionInterface $e) {
            throw new \Exception("Erreur client lors de l'appel à l'API : " . $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            throw new \Exception("Erreur serveur lors de l'appel à l'API : " . $e->getMessage());
        }
    }
}
