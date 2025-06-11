<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mes-produits')]
#[IsGranted('ROLE_USER')]
class MyProductController extends AbstractController
{
  public function __construct(private NotificationService $notificationService) {}
  #[Route('/', name: 'app_my_products')]
  public function index(ProduitRepository $produitRepository): Response
  {
    $produits = $produitRepository->findByCreatedBy($this->getUser());

    return $this->render('my_product/index.html.twig', [
      'produits' => $produits,
    ]);
  }

  #[Route('/nouveau', name: 'app_my_product_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    if ($request->isMethod('POST')) {
      $produit = new Produit();
      $produit->setNom($request->request->get('nom'));
      $produit->setPrix((int) $request->request->get('prix'));
      $produit->setCategory($request->request->get('category'));
      $produit->setDescription($request->request->get('description'));
      $produit->setCreatedBy($this->getUser());

      $this->notificationService->createProductNotification('created', $produit->getNom(), $produit->getCreatedBy());

      $entityManager->persist($produit);
      $entityManager->flush();

      $this->addFlash('success', 'Produit créé avec succès !');
      return $this->redirectToRoute('app_my_products');
    }

    return $this->render('my_product/form.html.twig', [
      'produit' => null,
    ]);
  }

  #[Route('/{id}/modifier', name: 'app_my_product_edit', methods: ['GET', 'POST'])]
  public function edit(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
  {
    if ($produit->getCreatedBy() !== $this->getUser()) {
      throw $this->createAccessDeniedException();
    }

    if ($request->isMethod('POST')) {
      $produit->setNom($request->request->get('nom'));
      $produit->setPrix((int) $request->request->get('prix'));
      $produit->setCategory($request->request->get('category'));
      $produit->setDescription($request->request->get('description'));
      $this->notificationService->createProductNotification('updated', $produit->getNom(), $produit->getCreatedBy());

      $entityManager->flush();

      $this->addFlash('success', 'Produit modifié avec succès !');
      return $this->redirectToRoute('app_my_products');
    }

    return $this->render('my_product/form.html.twig', [
      'produit' => $produit,
    ]);
  }

  #[Route('/{id}/supprimer', name: 'app_my_product_delete', methods: ['POST'])]
  public function delete(Produit $produit, EntityManagerInterface $entityManager): Response
  {
    if ($produit->getCreatedBy() !== $this->getUser()) {
      throw $this->createAccessDeniedException();
    }

    $entityManager->remove($produit);
    $this->notificationService->createProductNotification('deleted', $produit->getNom(), $produit->getCreatedBy());
    $entityManager->flush();

    $this->addFlash('success', 'Produit supprimé avec succès !');
    return $this->redirectToRoute('app_my_products');
  }
}
