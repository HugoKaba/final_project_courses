<?php

namespace App\Controller;

use App\Message\AddPointsToUsersMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messenger')]
#[IsGranted('ROLE_ADMIN')]
class MessengerController extends AbstractController
{
  #[Route('/add-points', name: 'app_messenger_add_points', methods: ['POST'])]
  public function addPointsToActiveUsers(MessageBusInterface $bus): Response
  {
    $bus->dispatch(new AddPointsToUsersMessage(1000));

    $this->addFlash('success', 'La distribution de 1000 points aux utilisateurs actifs a été programmée en arrière-plan.');
    return $this->redirectToRoute('app_admin_dashboard');
  }
}
