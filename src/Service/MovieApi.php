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

    // get a single movie by id
    public function fetchMovieById(int $id): array
    {
        $response = $this->client->request('GET', "https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas/$id?form=json");

        return $response->toArray();
    }

    // get all movies for certain fields
    public function fetchMovies(): array
    {

        $response = $this->client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&fields=title,description,programType,plprogram$tags,plprogram$thumbnails,plprogram$year');

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

    // get movies by genre
    // public function fetchMoviesByGenre(string $genre, int $limit = 10): array
    // {   

    //     $range = "1-$limit";

    //     if ($limit == -1 ){
    //         $range = "1-100";
    //     }

    //     $response = $this->client->request('GET', "https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&byTags=genre:$genre&range=$range");

    //     return $response->toArray();
    // }   


}
