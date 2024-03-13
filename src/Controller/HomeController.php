<?php

namespace App\Controller;

use App\Repository\BrandsRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private function getCartItemCount(): int {
        $user = $this->getUser();
        if ($user) {
            $cart = $user->getActiveCart(); // Assuming getActiveCart() returns the active cart
            return count($cart->getCartItems());
        }
        return 0;
    }

    #[Route('/', name: 'app_home')]
    public function index(BrandsRepository $brandsRepository, ProductsRepository $productsRepository): Response
    {


        $brands = $brandsRepository->findBy(['status' => true], ['created_at' => 'ASC']);
        $on_sale = $productsRepository->findBy(['on_sale' => true, 'status' => true], ['created_at' => 'DESC']);
        $topViews = $productsRepository->findBy([], ['views' => 'DESC'], 12);

        return $this->render('public/homepage.html.twig', [
            'brands' => $brands,
            'on_sale' => $on_sale,
            'cartItemCount' => $this->getCartItemCount(),
            'topViews' => $topViews,

        ]);
    }


    #[Route('/contact', name: 'app_contact')]
    public function contact()
    {
        return $this->render('public/pages/contact.html.twig');
    }
}
