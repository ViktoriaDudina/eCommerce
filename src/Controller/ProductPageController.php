<?php

// src/Controller/ProductPageController.php

// src/Controller/ProductPageController.php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductPageController extends AbstractController
{
    private function getCartItemCount(): int {
        $user = $this->getUser();
        if ($user) {
            $cart = $user->getActiveCart(); // Assuming getActiveCart() returns the active cart
            return count($cart->getCartItems());
        }
        return 0;
    }


    #[Route('/products', name: 'app_product_page')]
    public function index(Request $request, ProductsRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $category = $request->query->get('category');

        // Fetch all categories from the repository
        $categories = $categoryRepository->findAll();

        // Fetch products based on the category filter and status
        if ($category) {
            $products = $productRepository->findBy([
                'category' => $category,
                'status' => 1
            ]);
        } else {
            $products = $productRepository->findBy(['status' => 1]);
        }

        return $this->render('public/pages/products.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'cartItemCount' => $this->getCartItemCount(),
        ]);
    }
}
