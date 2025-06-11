<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Message\AddPointsToUsersMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddPointsToUsersMessageHandler
{
  public function __construct(
    private UserRepository $userRepository,
    private EntityManagerInterface $entityManager
  ) {}

  public function __invoke(AddPointsToUsersMessage $message): void
  {
    $activeUsers = $this->userRepository->findActiveUsers();

    foreach ($activeUsers as $user) {
      $user->setPoints($user->getPoints() + $message->getPoints());
      $this->entityManager->persist($user);

      $notification = new Notification();
      $notification->setLabel("Vous avez reçu {$message->getPoints()} points bonus le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'));
      $notification->setType(Notification::TYPE_POINTS);
      $notification->setUser($user);
      $notification->setConcernedUser($user);
      $this->entityManager->persist($notification);
    }

    $this->entityManager->flush();
  }
}
