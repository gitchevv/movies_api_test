<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\Cache;

use App\Service\MovieApi;


class MovieController extends AbstractController
{
    private $apiClient;

    public function __construct(MovieApi $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    #[Route('/', name: 'homepage')]
    public function homepage(MovieApi $apiClient): Response
    {   
        $genres = ['Action', 'Comedy', 'Thriller', 'War', 'Romance', 'Drama', 'Crime', 'Documentary', 'Horror'];
        $moviesByGenre = [];


        foreach ($genres as $genre) {
            $movies = $apiClient->fetchMoviesByGenre($genre);

            $moviesByGenre[$genre] = $movies['entries'];
        }
        
        // dd($moviesByGenre);
        return $this->render('movies/home.html.twig', [
            'moviesByGenre' => $moviesByGenre,
        ]);
    }

    #[Route('/genre/{genre}' , name: "genre_list")]
    public function genreDetail($genre, MovieApi $apiClient) {

        $genreDetails = $apiClient->fetchMoviesByGenre($genre, -1);
        $moviesInGenre = $genreDetails['entries'];

        if (!$genreDetails) {
            throw $this->createNotFoundException('The movie does not exist');
        }
        

        $count = count($moviesInGenre);
        // dd($moviesInGenre);

        return $this->render('movies/genre.html.twig', [
            'moviesInGenre' => $moviesInGenre,
            'movieCount' => $count,
            'genreName' => $genre
        ]);
    }

    // get a single movie from id
    #[Route('/movie/{id}', name: "single_movie")]
    public function movieDetail($id, MovieApi $apiClient): Response
    {   
        $movie = $apiClient->fetchMovieById($id);

        if (!$movie) {
            throw $this->createNotFoundException('The movie does not exist');
        }

        return $this->render('movies/single-movie.html.twig', [
            'movie' => $movie,
        ]);
    }



    // #[Route('/test', name: 'test')]
    // public function test(MovieApi $apiClient): Response
    // {
    //     $allMovies = $apiClient->fetchMovies();
    //     $moviesByGenre = $this->organizeMoviesByGenre($allMovies['entries']);
    //     dd($moviesByGenre);
        
    //     return $this->render('movies/test.html.twig', [
    //         'moviesByGenre' => $moviesByGenre,
    //     ]);
    // }

    // private function organizeMoviesByGenre(array $movies): array
    // {
    //     $genreList = ['Action', 'Comedy', 'Thriller', 'War', 'Romance', 'Drama', 'Crime', 'Documentary', 'Horror'];
    //     $moviesByGenre = [];
    
    //     // Initialize each genre in the list with an empty array
    //     foreach ($genreList as $genre) {
    //         $moviesByGenre[$genre] = [];
    //     }
    
    //     foreach ($movies as $movie) {
    //         // Extract the genres from the tags
    //         $tags = $movie['plprogram$tags'] ?? [];
            
    //         foreach ($tags as $tag) {
    //             $genreName = $tag['plprogram$title'] ?? '';
    
    //             // Check if the tag's title is in our list of genres
    //             if (in_array($genreName, $genreList)) {
    //                 // If yes, add the movie to the corresponding genre
    //                 $moviesByGenre[$genreName][] = $movie;
    //             }
    //         }
    //     }
    
    //     return $moviesByGenre;
    // }



    // #[Route('/movies', name: 'movie_list')]
    // public function list(): Response
    // {
    //     $fetchAll = $this->apiClient->fetchMovies();
    //     $movies = $fetchAll['entries'];
    //     $moviesCollection = [];

    //     foreach ($movies as $movie) {

    //         if ($movie['plprogram$scheme'] == 'genre') {
    //             foreach ($movie['plprogram$tags'] as $tags) {

    //                 var_dump($tags);
    //                 $movieCollection[$tags['plprogram$title']] = $tags['plprogram$title'];

    //             }
    //         }


    //     }

    //     dd($moviesCollection);
    // }
}
