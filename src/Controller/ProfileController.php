<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileUpdateType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\OrdersRepository;
use App\Repository\OrderLineRepository;

class ProfileController extends AbstractController
{

    private function getCartItemCount(): int {
        $user = $this->getUser();
        if ($user) {
            $cart = $user->getActiveCart(); // Assuming getActiveCart() returns the active cart
            return count($cart->getCartItems());
        }
        return 0;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(OrdersRepository $ordersRepository, OrderLineRepository $orderLineRepository): Response
    {
        $user = $this->getUser();

        $ordersWithTotals = [];
        $orders = $ordersRepository->findBy(['user' => $user]);

        foreach ($orders as $order) {
            $orderLines = $orderLineRepository->findBy(['orders' => $order]);
            $total = 0;

            foreach ($orderLines as $line) {
                $total += $line->getQuantity() * $line->getPrice();
            }

            $ordersWithTotals[] = [
                'order' => $order,
                'lines' => $orderLines,
                'total' => $total
            ];
        }

        return $this->render('public/profile/index.html.twig', [
            'user' => $user,
            'ordersWithTotals' => $ordersWithTotals,
            'cartItemCount' => $this->getCartItemCount(),
        ]);
    }

    #[Route('/profile/update', name: 'profile_update')]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUpdateType::class, $user);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();

            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatar->guessExtension();

                $avatar->move(
                    $this->getParameter('profile_directory'),
                    $newFilename
                );
                if ($user->getAvatar()) {
                    unlink($this->getParameter("profile_directory") . "/" . $user->getAvatar());
                }
                $user->setAvatar($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('public/profile/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/delete', name: 'profile_delete')]
    public function deleteAccount(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        $user = $this->getUser();

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();

            // Logout the user
            $tokenStorage->setToken(null);
            $session->invalidate();

            sweetalert()->addWarning('Sorry to know that you just Deleted account Successfully.You can come back any Time');
            return $this->redirectToRoute('app_home');
        }

        // Redirect to an error page or handle the case where the user is not logged in
        return $this->redirectToRoute('app_home');
    }
}
