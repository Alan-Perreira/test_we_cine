<?php

namespace App\Tests\Controller;

use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MovieControllerTest extends WebTestCase
{
    public function testIndexActionReturnsResponseWithVideo()
    {
        // Créez un client de test
        $client = static::createClient();

        /** @var TmdbApiService|\PHPUnit\Framework\MockObject\MockObject $tmdbApiService */
        $tmdbApiService = $this->createMock(TmdbApiService::class);

        // Simulez le retour de genres
        $tmdbApiService->method('getGenres')->willReturn([
            'genres' => [['id' => 1, 'name' => 'Action']]
        ]);

        // Simulez le retour de films avec une bande-annonce
        $tmdbApiService->method('getMoviesByGenre')->willReturn([
            'results' => [
                ['id' => 1, 'title' => 'Film d\'action', 'overview' => 'Un film d\'action.']
            ]
        ]);

        $tmdbApiService->method('getMovieVideos')->willReturn([
            'results' => [
                ['type' => 'Trailer', 'site' => 'YouTube', 'key' => '123456']
            ]
        ]);

        // Enregistrez le mock dans le conteneur de services
        $client->getContainer()->set(TmdbApiService::class, $tmdbApiService);

        // Effectuez la requête sur la route index
        $client->request('GET', '/');

        // Vérifiez que la réponse est OK
        $this->assertResponseIsSuccessful();
        $this->assertNotNull($client->getResponse()->getContent());
    }

    public function testIndexActionReturnsResponseWithoutVideo()
    {
        // Créez un client de test
        $client = static::createClient();

        /** @var TmdbApiService|\PHPUnit\Framework\MockObject\MockObject $tmdbApiService */
        $tmdbApiService = $this->createMock(TmdbApiService::class);

        // Simulez le retour de genres
        $tmdbApiService->method('getGenres')->willReturn([
            'genres' => [['id' => 1, 'name' => 'Action']]
        ]);

        // Simulez le retour de films sans bande-annonce
        $tmdbApiService->method('getMoviesByGenre')->willReturn([
            'results' => [
                ['id' => 1, 'title' => 'Film d\'action', 'overview' => 'Un film d\'action.']
            ]
        ]);

        $tmdbApiService->method('getMovieVideos')->willReturn([
            'results' => [] // Pas de vidéos
        ]);

        // Enregistrez le mock dans le conteneur de services
        $client->getContainer()->set(TmdbApiService::class, $tmdbApiService);

        // Effectuez la requête sur la route index
        $client->request('GET', '/');

        // Vérifiez que la réponse est OK
        $this->assertResponseIsSuccessful();
        $this->assertNotNull($client->getResponse()->getContent());
    }

    public function testIndexActionReturnsResponseWithNoMovies()
    {
        // Créez un client de test
        $client = static::createClient();

        /** @var TmdbApiService|\PHPUnit\Framework\MockObject\MockObject $tmdbApiService */
        $tmdbApiService = $this->createMock(TmdbApiService::class);

        // Simulez le retour de genres
        $tmdbApiService->method('getGenres')->willReturn([
            'genres' => [['id' => 1, 'name' => 'Action']]
        ]);

        // Simulez le retour de films vides
        $tmdbApiService->method('getMoviesByGenre')->willReturn([
            'results' => [] // Pas de films
        ]);

        // Enregistrez le mock dans le conteneur de services
        $client->getContainer()->set(TmdbApiService::class, $tmdbApiService);

        // Effectuez la requête sur la route index
        $client->request('GET', '/');

        // Vérifiez que la réponse est OK
        $this->assertResponseIsSuccessful();
        $this->assertNotNull($client->getResponse()->getContent());
    }
}
