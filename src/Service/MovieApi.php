<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MovieApi
{
    private $client;
    private $cache;

    public function __construct(HttpClientInterface $client, CacheInterface $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
    }

    // get all movies
    public function fetchMovies(): array
    {

        $cacheKey = 'all_movies'; 

        return $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(3600); 

            
            $response = $this->client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&byProgramType=movie');

            return $response->toArray();
        });
    }

    // get a single movie by id
    public function fetchMovieById(int $id): array
    {
        $response = $this->client->request('GET', "https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas/$id?form=json");

        return $response->toArray();
    }



    public function fetchMoviesByGenre(string $genre, int $limit = 10): array
    {
        $cacheKey = sprintf('movies_by_genre_%s_%d', $genre, $limit);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($genre, $limit) {
            $item->expiresAfter(3600);

            $range = $limit == -1 ? "1-100" : "1-$limit";
            $response = $this->client->request('GET', "https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&byTags=genre:$genre&range=$range");

            return $response->toArray();
        });
    }
}
