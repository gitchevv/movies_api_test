<?php

namespace App\Controller;

use App\Service\MovieApi;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WishlistController extends AbstractController
{

    // Adds a movies to an array (wishlist) and save it in session storage
    #[Route('/wishlist/add/{id}', name: 'add_to_wishlist')]
    public function addToWishlist($id, SessionInterface $session): Response
    {
        $wishlist = $session->get('wishlist', []);

        if (!in_array($id, $wishlist)) {
            $wishlist[] = $id;
            $session->set('wishlist', $wishlist);
        }

        return $this->redirectToRoute('wishlist_view');
    }

    // DELETE
    #[Route('/wishlist/delete/{id}', name: 'delete_from_wishlist')]
    public function deleteFromWishlist($id, SessionInterface $session): Response
    {
        $wishlist = $session->get('wishlist', []);

        // search for the $id passed from the Remove button
        // if there's a match, remove it from the wishlist by key
        if ( (array_search($id, $wishlist)) !== false) {

            $key = array_search($id, $wishlist);

            unset($wishlist[$key]);
        }

        $wishlist = array_values($wishlist);
        $session->set('wishlist', $wishlist);

        return $this->redirectToRoute('wishlist_view');
    }

    // Show wishlist
    #[Route('/wishlist', name: 'wishlist_view')]
    public function viewWishlist(SessionInterface $session, MovieApi $apiClient): Response
    {
        $wishlistIds = $session->get('wishlist', []);
        $wishlist = [];

        foreach ($wishlistIds as $id) {
            $wishlist[] = $apiClient->fetchMovieById($id);
        }
        // dd($movies);
        return $this->render('wishlist/view.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }
}
