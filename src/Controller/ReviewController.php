<?php

namespace App\Controller;

use App\Entity\Reviews;
use App\Entity\Products;
use App\Form\ReviewType;
use App\Repository\ReviewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewController extends AbstractController
{

    #[Route('/admin/reviews', name: 'app_review_index', methods: ['GET'])]
    public function index(ReviewsRepository $reviewsRepository): Response
    {
        return $this->render('admin/review/index.html.twig', [
            'reviews' => $reviewsRepository->findBy([], ['created_at' => 'DESC']) ,
        ]);
    }

    #[Route('/admin/reviews/{id}', name: 'app_review_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Reviews $reviews, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reviews->getId(), $request->request->get('_token'))) {
            $user = $reviews->getUser();
            if ($user) {
                $user->removeReview($reviews);
            }
    
            $product = $reviews->getProducts();
            $product->removeReview($reviews);
    
            $entityManager->remove($reviews);
            $entityManager->flush();
    
            flash()->addSuccess('Deleted Successfully');
        }
        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }




    #[Route('/review/delete/{id}', name: 'app_review_delete', methods: ['POST', 'DELETE'])]
    public function deleteReview(Reviews $review, EntityManagerInterface $entityManager, Request $request, AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $this->getUser();
    
        // Check if the user is authenticated and is the owner of the review
        if ($user && $user->getId() === $review->getUser()->getId()) {
            $entityManager->remove($review);
            $entityManager->flush();
    
            $this->addFlash('success', 'Review deleted successfully.');
        } else {
            // You can customize the error message or redirect here if needed
            $this->addFlash('error', 'You are not allowed to delete this review.');
        }
    
        return $this->redirectToRoute('app_product_details', ['id' => $review->getProducts()->getId()]);
    }
}
