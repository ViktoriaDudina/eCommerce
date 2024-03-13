<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{

    
    private function getCartItemCount(): int {
        $user = $this->getUser();
        if ($user) {
            $cart = $user->getActiveCart(); // Assuming getActiveCart() returns the active cart
            return count($cart->getCartItems());
        }
        return 0;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'cartItemCount' => $this->getCartItemCount(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            flash()->addSuccess('New User Added Successfully');


            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            flash()->addSuccess('User Deleted Successfully');

        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/{id}/block', name: 'user_block')]

    public function blockUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Implement user blocking logic here
        // Ensure that the current user has ROLE_ADMIN

        $user->setStatus(false);
        $entityManager->flush();
        flash()->addSuccess('User Block Successfully');



        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/unblock', name: 'user_unblock')]
    public function unblockUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Implement user unblocking logic here
        // Ensure that the current user has ROLE_ADMIN

        $user->setStatus(true);
        $entityManager->flush();
        flash()->addSuccess('User Unblocked Successfully');


        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/toggle-role-admin', name: 'user_toggle_role_admin')]
public function toggleRoleAdmin(User $user, EntityManagerInterface $entityManager): Response
{
    $user->setRoles(['ROLE_ADMIN']);
    $entityManager->flush();
    flash()->addSuccess('User Role Change to Admin Successfully');


    return $this->redirectToRoute('app_user_index');
}

#[Route('/{id}/toggle-role-user', name: 'user_toggle_role_user')]
public function toggleRoleUser(User $user, EntityManagerInterface $entityManager): Response
{
    $user->setRoles(['ROLE_USER']);
    $entityManager->flush();
    flash()->addSuccess('User Role Change to User Successfully');


    return $this->redirectToRoute('app_user_index');
}
}
