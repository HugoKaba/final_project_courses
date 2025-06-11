<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Entity\User;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
  public function __construct(private NotificationService $notificationService) {}
  #[Route('/', name: 'app_admin_dashboard')]
  public function dashboard(): Response
  {
    return $this->render('admin/dashboard.html.twig');
  }

  #[Route('/users', name: 'app_admin_users')]
  public function users(UserRepository $userRepository): Response
  {
    $users = $userRepository->findAll();

    return $this->render('admin/users.html.twig', [
      'users' => $users,
    ]);
  }

  #[Route('/user/{id}/toggle-active', name: 'app_admin_user_toggle_active', methods: ['POST'])]
  public function toggleUserActive(
    User $user,
    EntityManagerInterface $entityManager,
    \App\Service\NotificationService $notificationService
  ): Response {
    $oldStatus = $user->isActif();
    $user->setActif(!$user->isActif());

    $entityManager->persist($user);
    $entityManager->flush();

    if ($oldStatus !== $user->isActif()) {
      $notificationService->createUserStatusNotification($user, $user->isActif());
    }

    $status = $user->isActif() ? 'activé' : 'désactivé';
    $this->addFlash('success', "L'utilisateur {$user->getPrenom()} {$user->getNom()} a été $status.");
    return $this->redirectToRoute('app_admin_users');
  }

  #[Route('/produits', name: 'app_admin_produits')]
  public function produits(ProduitRepository $produitRepository): Response
  {
    $produits = $produitRepository->findAll();

    return $this->render('admin/produits.html.twig', [
      'produits' => $produits,
    ]);
  }
  #[Route('/produit/new', name: 'app_admin_produit_new', methods: ['GET', 'POST'])]
  public function newProduit(Request $request, EntityManagerInterface $entityManager): Response
  {
    if ($request->isMethod('POST')) {
      $produit = new Produit();
      $produit->setNom($request->request->get('nom'));
      $produit->setPrix((int) $request->request->get('prix'));
      $produit->setCategory($request->request->get('category'));
      $produit->setDescription($request->request->get('description'));
      $produit->setCreatedBy($this->getUser());

      $entityManager->persist($produit);
      $this->notificationService->createProductNotification('created', $produit->getNom(), $produit->getCreatedBy());
      $entityManager->flush();

      $this->addFlash('success', 'Produit créé avec succès !');
      return $this->redirectToRoute('app_admin_produits');
    }

    return $this->render('admin/produit_form.html.twig', [
      'produit' => null,
    ]);
  }

  #[Route('/produit/{id}/edit', name: 'app_admin_produit_edit', methods: ['GET', 'POST'])]
  public function editProduit(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
  {
    if (!$this->isGranted('ROLE_ADMIN') && $produit->getCreatedBy() !== $this->getUser()) {
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
      return $this->redirectToRoute('app_admin_produits');
    }

    return $this->render('admin/produit_form.html.twig', [
      'produit' => $produit,
    ]);
  }

  #[Route('/produit/{id}/delete', name: 'app_admin_produit_delete', methods: ['POST'])]
  public function deleteProduit(Produit $produit, EntityManagerInterface $entityManager): Response
  {
    if (!$this->isGranted('ROLE_ADMIN') && $produit->getCreatedBy() !== $this->getUser()) {
      throw $this->createAccessDeniedException();
    }

    $entityManager->remove($produit);
    $this->notificationService->createProductNotification('deleted', $produit->getNom(), $produit->getCreatedBy());
    $entityManager->flush();

    $this->addFlash('success', 'Produit supprimé avec succès !');
    return $this->redirectToRoute('app_admin_produits');
  }
}
