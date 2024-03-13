<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Form\BrandsType;
use App\Repository\BrandsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/brands')]
class BrandsController extends AbstractController
{
    #[Route('/', name: 'app_brands_index', methods: ['GET'])]
    public function index(BrandsRepository $brandsRepository): Response
    {
        return $this->render('admin/brands/index.html.twig', [
            'brands' => $brandsRepository->findBy([], ['created_at' => 'DESC']) ,
        ]);
    }

    #[Route('/new', name: 'app_brands_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $brand = new Brands();
        $form = $this->createForm(BrandsType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandimage = $form->get('image')->getData();

            if ($brandimage) {
                $originalFilename = pathinfo($brandimage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brandimage->guessExtension();

                $brandimage->move(
                        $this->getParameter('brands_directory'),
                        $newFilename);
                }
                $brand->setImage($newFilename);
            $entityManager->persist($brand);
            $entityManager->flush();
            flash()->addSuccess('Added Successfully');


            return $this->redirectToRoute('app_brands_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/brands/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_brands_show', methods: ['GET'])]
    public function show(Brands $brand): Response
    {
        return $this->render('admin/brands/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_brands_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brands $brand, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BrandsType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandimage = $form->get('image')->getData();

            if ($brandimage) {
                $originalFilename = pathinfo($brandimage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brandimage->guessExtension();

                $brandimage->move(
                        $this->getParameter('brands_directory'),
                        $newFilename);
                        if ($brand->getImage()) {
                            unlink($this->getParameter("brands_directory") . "/" . $brand->getImage());
                         }
                         $brand->setImage($newFilename);
                }
            $entityManager->flush();
            flash()->addSuccess('Updated Successfully');


            return $this->redirectToRoute('app_brands_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/brands/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_brands_delete', methods: ['POST'])]
    public function delete(Request $request, Brands $brand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            if ($brand->getImage()) {
                unlink($this->getParameter("brands_directory") . "/" . $brand->getImage());
             }
            $entityManager->remove($brand);
            $entityManager->flush();
            flash()->addSuccess('Deleted Successfully');

        }

        return $this->redirectToRoute('app_brands_index', [], Response::HTTP_SEE_OTHER);
    }
}
