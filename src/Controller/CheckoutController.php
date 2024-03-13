<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Orders;
use App\Entity\OrderLine;
use App\Entity\Carts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class CheckoutController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $user->getActiveCart();
        if (!$cart) {
            return $this->redirectToRoute('some_route_if_cart_is_empty');
        }

        // Refresh the cart entity
        $entityManager->refresh($cart);

        $cartItems = $cart->getCartItems();
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->getQuantity() * $item->getProduct()->getPrice();
        }

        return $this->render('public/cart/checkout.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
    
   
    
    #[Route('/create-order', name: 'app_create_order', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $user->getActiveCart();
        if (!$cart || $cart->getCartItems()->isEmpty()) {
            // Handle the case where the cart is empty or doesn't exist
            return $this->redirectToRoute('app_cart');
        }

        // Create a new Orders entity
        $order = new Orders();
        $order->setUser($user);
        $order->setCreatedAt(new \DateTimeImmutable());

        // Process each cart item and add to the order
        foreach ($cart->getCartItems() as $cartItem) {
            $orderLine = new OrderLine();
            $orderLine->setOrders($order);
            $orderLine->setProducts($cartItem->getProduct()); // Using getProduct instead of getProducts
            $orderLine->setQuantity($cartItem->getQuantity());

            $entityManager->persist($orderLine);
            $order->addOrderLine($orderLine);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        $this->sendOrderConfirmationEmail($order, $mailer);

        $this->clearCart($cart, $entityManager);

        // TODO: Clear the cart after successful order creation

        // Redirect to a confirmation page or display a success message
        return $this->redirectToRoute('order_success'); // Replace with your success route
    }

    #[Route('/order-success', name: 'order_success')]
    public function orderSuccess(): Response
    {
        $user = $this->security->getUser();

        return $this->render('public/cart/success.html.twig', [
            'user' => $user,
        ]);
    }

    private function clearCart(Carts $cart, EntityManagerInterface $entityManager): void
    {
        foreach ($cart->getCartItems() as $cartItem) {
            $entityManager->remove($cartItem);
        }

        $entityManager->flush();
    }

    private function sendOrderConfirmationEmail(Orders $order, MailerInterface $mailer)
{
    $email = (new Email())
        ->from('ahello4321@gmail.com')
        ->to($order->getUser()->getEmail())
        ->subject('Order Confirmation')
        ->html($this->renderView('mail/order_confirmation.html.twig', [
            'order' => $order
        ]));

    try {
        $mailer->send($email);
    } catch (TransportExceptionInterface $e) {
        // Handle email sending error
    }
}
}
