<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Carts;
use App\Entity\CartItems;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CartController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function getCartItemCount(): int
    {
        $user = $this->getUser();
        if ($user) {
            $cart = $user->getActiveCart(); // Assuming getActiveCart() returns the active cart
            return count($cart->getCartItems());
        }
        return 0;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Handle the case where there is no authenticated user
            return $this->redirectToRoute('app_login');
        }

        $cart = $user->getActiveCart();
        $cartItems = $cart->getCartItems();

        return $this->render('public/cart/index.html.twig', [
            'cartItems' => $cartItems,
            'cartItemCount' => $this->getCartItemCount(),
            'total' => $cart->getTotal(),
        ]);
    }

    #[Route('/cart/add/{productId}/{quantity}', name: 'app_add_to_cart',  defaults: ['quantity' => 1])]
    public function addToCart(int $productId, int$quantity, ProductsRepository $productRepository, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $user = $this->getUser();

        if (!$user) {
            // Handle the case where there is no authenticated user
            return $this->redirectToRoute('app_login');
        }

        $cart = $user->getActiveCart();

        $product = $productRepository->find($productId);
        if (!$product) {
            // Handle the case where the product doesn't exist
            return $this->redirectToRoute('product_list');
        }

        $existingCartItem = $cart->getCartItems()->filter(
            function (CartItems $item) use ($product) {
                return $item->getProduct() === $product;
            }
        )->first();

        if ($existingCartItem) {
            // If item exists in cart, update quantity
            $existingCartItem->setQuantity($existingCartItem->getQuantity() + $quantity);
            $this->addFlash('success', 'Product quantity updated in your cart.');
        } else {
            // If item does not exist in cart, add a new CartItem
            $cartItem = new CartItems();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity); // Or any other logic for initial quantity
            $cartItem->setCarts($cart);
            $entityManager->persist($cartItem);
            $this->addFlash('success', 'Product added to your cart.');
        }

        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/remove/{cartItemId}', name: 'app_remove_from_cart')]
    public function removeFromCart(int $cartItemId): Response
    {
        $entityManager = $this->entityManager;
        $cartItem = $entityManager->getRepository(CartItems::class)->find($cartItemId);

        if ($cartItem) {
            // Ensure that the cart item belongs to the current user's cart
            if ($cartItem->getCarts()->getCreatedBy() === $this->getUser()) {
                $entityManager->remove($cartItem);
                $entityManager->flush();

                // Add a flash message for user feedback
                $this->addFlash('success', 'Item removed from cart.');
            } else {
                // Add a flash message for error feedback
                $this->addFlash('error', 'You cannot remove this item.');
            }
        } else {
            // Item not found
            $this->addFlash('error', 'Item not found in the cart.');
        }

        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/update/{cartItemId}', name: 'app_update_cart', methods: ['POST'])]
    public function updateCartItem(Request $request, int $cartItemId, LoggerInterface $logger): JsonResponse
    {
        $entityManager = $this->entityManager;
        $cartItem = $entityManager->getRepository(CartItems::class)->find($cartItemId);
    
        if (!$cartItem) {
            return $this->json([
                'success' => false,
                'message' => 'Item not found in the cart.'
            ]);
        }
    
        // Ensure that the cart item belongs to the current user's cart
        if ($cartItem->getCarts()->getCreatedBy() !== $this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'You cannot update this item.'
            ]);
        }
    
        $data = json_decode($request->getContent(), true);
        $newQuantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;
        
        $logger->info("Received quantity: " . $newQuantity);
    
        if ($newQuantity <= 0) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid quantity.'
            ]);
        }
    
        $cartItem->setQuantity($newQuantity);
        $entityManager->flush();
    
        // Calculate the updated total
        $total = $this->calculateCartTotal($cartItem->getCarts());
    
        // Return JSON response
        return $this->json([
            'success' => true,
            'newTotal' => $total
        ]);
    }

    // Helper method to calculate the cart total
    private function calculateCartTotal(Carts $cart): float
    {
        $total = 0;
        foreach ($cart->getCartItems() as $item) {
            $total += $item->getQuantity() * $item->getProduct()->getPrice();
        }
        return $total;
    }

    #[Route('/cart/clear', name: 'app_clear_cart', methods: ['POST'])]
    public function clearCart(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ]);
        }

        $cart = $user->getActiveCart();
        if (!$cart) {
            return $this->json([
                'success' => false,
                'message' => 'No active cart found.'
            ]);
        }

        foreach ($cart->getCartItems() as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        $this->addFlash('success', 'All items have been removed from your cart.');

        return $this->json(['success' => true]);
    }
}
