<?php

namespace App\Controller;

use App\Repository\BrandsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\ReviewsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin')]
    public function dashboard(OrdersRepository $ordersRepository,ReviewsRepository $reviewsRepository, ProductsRepository $productsRepository, BrandsRepository $brandsRepository): Response
    {

        $totalOrders = $ordersRepository->count([]);
        $totalReviews = $reviewsRepository->count([]);
        $totalProducts = $productsRepository->count([]);
        $totalBrands = $brandsRepository->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'totalOrders' => $totalOrders,
            'totalReviews' => $totalReviews,
            'totalProducts' => $totalProducts,
            'totalBrands' => $totalBrands,

            
        ]);
    }
}
