<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
  public function __construct(
    private EntityManagerInterface $entityManager
  ) {}

  public function createUserStatusNotification(User $user, bool $isActive): void
  {
    if ($isActive) {
      $this->createUserNotification(
        $user,
        "Votre compte a été réactivé le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
        Notification::TYPE_REACTIVATION
      );

      $this->createAdminNotifications(
        "L'utilisateur {$user->getPrenom()} {$user->getNom()} a été réactivé le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
        Notification::TYPE_REACTIVATION,
        $user
      );
    } else {
      $this->createUserNotification(
        $user,
        "Votre compte a été désactivé le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
        Notification::TYPE_BLOCAGE
      );

      $this->createAdminNotifications(
        "L'utilisateur {$user->getPrenom()} {$user->getNom()} a été désactivé le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
        Notification::TYPE_BLOCAGE,
        $user
      );
    }

    $this->entityManager->flush();
  }

  public function createProductNotification(string $action, string $productName, User $creator): void
  {
    $message = match ($action) {
      'created' => "Ajout d'un produit: {$productName} par {$creator->getPrenom()} {$creator->getNom()} le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
      'updated' => "Modification du produit: {$productName} le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
      'deleted' => "Suppression du produit: {$productName} le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
      default => "Action sur le produit: {$productName}"
    };

    $this->createAdminNotifications($message, Notification::TYPE_PRODUCT, $creator);

    if ($action === 'created' && !in_array('ROLE_ADMIN', $creator->getRoles())) {
      $this->createUserNotification(
        $creator,
        "Votre produit '{$productName}' a été créé avec succès le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
        Notification::TYPE_PRODUCT
      );
    }

    $this->entityManager->flush();
  }

  public function createPurchaseNotification(User $buyer, string $productName): void
  {
    $this->createAdminNotifications(
      "Achat effectué par {$buyer->getPrenom()} {$buyer->getNom()} - Produit: {$productName} le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
      Notification::TYPE_ACHAT,
      $buyer
    );

    $this->entityManager->flush();
  }

  public function createPointsNotification(User $user, int $points): void
  {
    $this->createUserNotification(
      $user,
      "Vous avez reçu {$points} points bonus le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'),
      Notification::TYPE_POINTS
    );

    $this->entityManager->flush();
  }

  private function createUserNotification(User $user, string $message, string $type): void
  {
    $notification = new Notification();
    $notification->setLabel($message);
    $notification->setType($type);
    $notification->setUser($user);
    $notification->setConcernedUser($user);

    $this->entityManager->persist($notification);
  }

  private function createAdminNotifications(string $message, string $type, ?User $concernedUser = null): void
  {
    $qb = $this->entityManager->getRepository(User::class)->createQueryBuilder('u');
    $adminUsers = $qb
      ->where('u.roles LIKE :role')
      ->setParameter('role', '%ROLE_ADMIN%')
      ->getQuery()
      ->getResult();

    foreach ($adminUsers as $admin) {
      if (in_array('ROLE_ADMIN', $admin->getRoles())) {
        $notification = new Notification();
        $notification->setLabel($message);
        $notification->setType($type);
        $notification->setUser($admin);
        $notification->setConcernedUser($concernedUser);

        $this->entityManager->persist($notification);
      }
    }
  }
}
