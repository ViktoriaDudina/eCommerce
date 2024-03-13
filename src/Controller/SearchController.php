<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, ProductsRepository $productRepository): Response
    {
        $query = $request->query->get('query');

        // Perform a search based on the query parameter
        $results = $productRepository->searchProducts($query);

        return $this->render('public/pages/search.html.twig', [
            'results' => $results,
            'query' => $query,
        ]);
    }
}
