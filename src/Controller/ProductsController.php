<?php

namespace App\Controller;

use App\Entity\Reviews;
use App\Entity\Products;
use App\Form\ReviewType;
use App\Entity\Wishlists;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

// #[Route('/admin/products')]
class ProductsController extends AbstractController
{
    private function getCartItemCount(): int {
        $user = $this->getUser();
        if ($user) {
            $cart = $user->getActiveCart(); // Assuming getActiveCart() returns the active cart
            return count($cart->getCartItems());
        }
        return 0;
    }

    #[Route('/admin/products', name: 'app_products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('admin/products/index.html.twig', [
            'products' => $productsRepository->findBy([], ['created_at' => 'DESC']),
        ]);
    }

    #[Route('/admin/products/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                $image->move(
                        $this->getParameter('products_directory'),
                        $newFilename);
                }else {
                    $newFilename = 'no-product.png';
                }
                
                $product->setImage($newFilename);
            
            $entityManager->persist($product);
            $entityManager->flush();
            flash()->addSuccess('Added Successfully');


            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/admin/products/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('admin/products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin/products/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $onSaleValue = $form->get('on_sale')->getData();
            // $product->setOnSale($onSaleValue);
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                $image->move(
                        $this->getParameter('products_directory'),
                        $newFilename);
                 if ($product->getImage() !="no-product.png") {
                    unlink($this->getParameter("products_directory") . "/" . $product->getImage());
                 }
                    
                    $product->setImage($newFilename);
                }
            $entityManager->flush();
            flash()->addSuccess('Updated Successfully');


            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/admin/products/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            if ($product->getImage() !="no-product.png" ) {
                unlink($this->getParameter("products_directory") . "/" . $product->getImage());
             }
            $entityManager->remove($product);
            $entityManager->flush();
            flash()->addSuccess('Deleted Successfully');

        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/product/details/{id}", name: "app_product_details")]

    public function detailsIndex(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $productRepository = $entityManager->getRepository(Products::class);

        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Increment the view count
         $product->incrementViews();
         $entityManager->persist($product);
         $entityManager->flush();

        $reviews = $product->getReviews();

        // Create an instance of the ReviewType form to pass to the template
    $review = new Reviews();
    $review->setProducts($product);
    $review->setUser($this->getUser());
        //   to add review 
    $form = $this->createForm(ReviewType::class, $review);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($review);
        $entityManager->flush();
        sweetalert()->addSuccess('Thank you for the Review');

        // Reload the page to show the updated reviews
        return $this->redirectToRoute('app_product_details', ['id' => $id]);
    }

        return $this->render('public/pages/detail.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
            'form' => $form->createView(), // Pass the form variable to the template
            'cartItemCount' => $this->getCartItemCount(),

        ]);
    }

    #[Route("/add-to-wishlist/{id}", name: "app_add_to_wishlist")]
public function addToWishlist(Request $request, Products $product, EntityManagerInterface $entityManager, RequestStack $requestStack,SessionInterface $session): Response
{
    $user = $this->getUser();
    $request = $requestStack->getCurrentRequest();

    if ($user) {
        // Check if the product is already in the user's wishlist
        $existingWishlistItem = $entityManager->getRepository(Wishlists::class)->findOneBy([
            'user' => $user,
            'product' => $product,
        ]);

        if ($existingWishlistItem) {
            $this->addFlash('info', 'Product is already in your wishlist.');
        } else {
            $wishlistItem = new Wishlists();
            $wishlistItem->setUser($user);
            $wishlistItem->setProduct($product);

            $entityManager->persist($wishlistItem);
            $entityManager->flush();

            $this->addFlash('success', 'Product added to wishlist.');
        }

        // Redirect back to the referring page or the product details page
        return $this->redirect($request->headers->get('referer'));
    } else {
        // Store the current URL in the session
        $session->set('referer', $request->headers->get('referer'));

        sweetalert()->addWarning('Please Login/Register to add product to Wishlist');

        // Example redirect to the login page
        return $this->redirectToRoute('app_login');
    }
 
}

#[Route("/wishlist", name: "app_wishlist")]
public function wishlist(EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    if (!$user) {
        // Redirect to the login page or show a message that the user needs to log in
        // ...

        sweetalert()->addWarning('Please Login/Register to view your wishlist');

        // Example redirect to the login page
        return $this->redirectToRoute('app_login');
    }
    
    $wishlistItems = $entityManager->getRepository(Wishlists::class)->findBy([
        'user' => $user,
    ]);


    return $this->render('public/wishlist/index.html.twig', [
        'wishlistItems' => $wishlistItems,
    ]);
}


#[Route("/remove-from-wishlist/{id}", name: "app_remove_from_wishlist")]
public function removeFromWishlist(Products $product, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    if ($user) {
        // Find the wishlist item to remove
        $wishlistItem = $entityManager->getRepository(Wishlists::class)->findOneBy([
            'user' => $user,
            'product' => $product,
        ]);

        if ($wishlistItem) {
            $entityManager->remove($wishlistItem);
            $entityManager->flush();

            $this->addFlash('success', 'Product removed from wishlist.');
        } else {
            $this->addFlash('info', 'Product is not in your wishlist.');
        }

        // Redirect back to the referring page or the product details page
        return $this->redirect($this->generateUrl('app_wishlist'));
    } else {
        // Example redirect to the login page
        return $this->redirectToRoute('app_login');
    }
}

#[Route('/admin/products/{id}/inActive', name: 'prduct_inactive')]

public function inactiveProduct(Products $products , EntityManagerInterface $entityManager): Response
{
    // Implement user blocking logic here
    // Ensure that the current user has ROLE_ADMIN

    $products->setStatus(false);
    $entityManager->flush();
    flash()->addSuccess('Product inActive Successfully');



    return $this->redirectToRoute('app_products_index');
}

#[Route('/admin/products/{id}/active', name: 'prduct_active')]
public function activeProduct(Products $products, EntityManagerInterface $entityManager): Response
{
    // Implement user unblocking logic here
    // Ensure that the current user has ROLE_ADMIN

    $products->setStatus(true);
    $entityManager->flush();
    flash()->addSuccess('Product Active Successfully');


    return $this->redirectToRoute('app_products_index');
}
}
