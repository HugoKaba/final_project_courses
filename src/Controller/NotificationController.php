<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
  #[Route('/', name: 'app_notifications')]
  public function index(NotificationRepository $notificationRepository): Response
  {
    $user = $this->getUser();

    $notifications = $notificationRepository->findBy(
      ['user' => $user],
      ['createdAt' => 'DESC']
    );

    return $this->render('notification/index.html.twig', [
      'notifications' => $notifications,
    ]);
  }
}
