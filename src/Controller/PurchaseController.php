<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/purchase')]
class PurchaseController extends AbstractController
{
  public function __construct(private UserRepository $userRepository, private readonly NotificationService $notificationService) {}
  #[Route('/{id}', name: 'app_purchase_product', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function purchaseProduct(
    Produit $produit,
    EntityManagerInterface $entityManager,
    UserRepository $userRepository
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if (!$user->isActif()) {
      $this->addFlash('error', 'Votre compte est désactivé, vous ne pouvez pas acheter de produits.');
      return $this->redirectToRoute('app_produit_show', ['id' => $produit->getId()]);
    }

    if ($user->getPoints() < $produit->getPrix()) {
      $this->addFlash('error', 'Vous n\'avez pas assez de points pour acheter ce produit.');
      return $this->redirectToRoute('app_produit_show', ['id' => $produit->getId()]);
    }
    $user->setPoints($user->getPoints() - $produit->getPrix());
    $this->notificationService->createPurchaseNotification($user, $produit->getNom());

    $entityManager->flush();

    $this->addFlash('success', 'Achat effectué avec succès !');
    return $this->redirectToRoute('app_produit_list');
  }
}
