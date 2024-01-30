<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieApi
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
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

    // get movies by genre
    public function fetchMoviesByGenre(string $genre, int $limit = 10): array
    {   
        
        $range = "1-$limit";

        if ($limit == -1 ){
            $range = "1-100";
        }

        $response = $this->client->request('GET', "https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&byTags=genre:$genre&range=$range");
            
        return $response->toArray();
    }   


}
