<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/notifications')]
#[IsGranted('ROLE_ADMIN')]
class NotificationController extends AbstractController
{
  #[Route('/', name: 'app_admin_notifications')]
  public function index(NotificationRepository $notificationRepository): Response
  {
    /** @var User $admin */
    $admin = $this->getUser();

    $notifications = $notificationRepository->findBy(
      ['user' => $admin],
      ['createdAt' => 'DESC']
    );

    return $this->render('admin/notifications.html.twig', [
      'notifications' => $notifications,
    ]);
  }

  #[Route('/latest-products', name: 'app_admin_notifications_latest_products', methods: ['GET'])]
  public function getLatestProductNotifications(NotificationRepository $notificationRepository): Response
  {
    /** @var User $admin */
    $admin = $this->getUser();

    $productNotifications = $notificationRepository->findBy(
      ['user' => $admin, 'type' => Notification::TYPE_PRODUCT],
      ['createdAt' => 'DESC'],
      5 // Dernières 5 notifications de produits
    );

    return $this->json([
      'notifications' => array_map(function ($notif) {
        return [
          'id' => $notif->getId(),
          'label' => $notif->getLabel(),
          'type' => $notif->getType(),
          'createdAt' => $notif->getCreatedAt()->format('d/m/Y H:i')
        ];
      }, $productNotifications)
    ]);
  }
}
