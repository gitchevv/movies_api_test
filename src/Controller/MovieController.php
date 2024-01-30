<?php
namespace App\Controller;

use App\Service\MovieApi;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MovieController extends AbstractController
{
    private $apiClient;

    public function __construct(MovieApi $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    //Route for home (movie list)
    #[Route('/', name: "home")]
    public function getAllMovies(PaginatorInterface $paginator, Request $request, MovieApi $apiClient): Response
    {
        $movies = $apiClient->fetchMovies();

        $pagination = $paginator->paginate(
            $movies['entries'], // the array to paginate
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('movies/movie-list.html.twig', [
            'movie' => $movies,
            'pagination' => $pagination

        ]);
    }

    // Genres page
    #[Route('/genres', name: 'genres')]
    public function getGenres(MovieApi $apiClient): Response
    {
        $genres = ['Action', 'Comedy', 'Thriller', 'War', 'Romance', 'Drama', 'Crime', 'Documentary', 'Horror'];
        $moviesByGenre = [];


        foreach ($genres as $genre) {
            $movies = $apiClient->fetchMoviesByGenre($genre);

            $moviesByGenre[$genre] = $movies['entries'];
        }

        return $this->render('movies/categories.html.twig', [
            'moviesByGenre' => $moviesByGenre,
        ]);
    }

    // Movies in genre
    #[Route('/genre/{genre}', name: "genre_list")]
    public function genreDetail(PaginatorInterface $paginator, Request $request, string $genre, MovieApi $apiClient)
    {

        $genreDetails = $apiClient->fetchMoviesByGenre($genre, -1);
        $moviesInGenre = $genreDetails['entries'];


        $count = count($moviesInGenre);

        $pagination = $paginator->paginate(
            $moviesInGenre, // the array to paginate
            $request->query->getInt('page', 1),          // current page number
            10          // limit of results per page
        );

        return $this->render('movies/genre.html.twig', [
            'moviesInGenre' => $moviesInGenre,
            'movieCount' => $count,
            'genreName' => $genre,
            'pagination' => $pagination
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
}
