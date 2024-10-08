<?php

namespace App\Tests\Controller;

use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieSearchTest extends WebTestCase
{
    public function testSearchMovieReturnsResults()
    {
        $client = static::createClient();

        // Créer un mock du service TmdbApiService
        $mockTmdbApiService = $this->createMock(TmdbApiService::class);

        // Configurer le mock pour renvoyer des résultats lors de l'appel à searchMovie
        $mockTmdbApiService->method('searchMovie')->willReturn([
            'results' => [
                ['id' => 1, 'title' => 'Inception', 'overview' => 'A thief who steals corporate secrets through the use of dream-sharing technology.']
            ]
        ]);

        // Enregistrer le mock dans le conteneur de service
        $client->getContainer()->set(TmdbApiService::class, $mockTmdbApiService);

        // Effectuer une requête sur la route de recherche
        $client->request('GET', '/search?query=Inception');

        // Assertions
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('results', $data);
        $this->assertCount(1, $data['results']);
        $this->assertEquals('Inception', $data['results'][0]['title']);
    }

    public function testSearchMovieReturnsNoResults()
    {
        $client = static::createClient();

        // Créer un mock du service TmdbApiService
        $mockTmdbApiService = $this->createMock(TmdbApiService::class);

        // Configurer le mock pour renvoyer une réponse vide lors de l'appel à searchMovie
        $mockTmdbApiService->method('searchMovie')->willReturn([
            'results' => [] // Pas de résultats
        ]);

        // Enregistrer le mock dans le conteneur de service
        $client->getContainer()->set(TmdbApiService::class, $mockTmdbApiService);

        // Effectuer une requête sur la route de recherche
        $client->request('GET', '/search?query=NotARealMovieTitle');

        // Assertions
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('results', $data);
        $this->assertCount(0, $data['results']);
    }
}

