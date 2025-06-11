<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  private UserPasswordHasherInterface $userPasswordHasher;

  public function __construct(UserPasswordHasherInterface $userPasswordHasher)
  {
    $this->userPasswordHasher = $userPasswordHasher;
  }

  public function load(ObjectManager $manager): void
  {
    $users = []; // Stocker les utilisateurs pour les notifications

    $admin = new User();
    $admin->setEmail('admin@example.com');
    $admin->setNom('Admin');
    $admin->setPrenom('Super');
    $admin->setRoles(['ROLE_ADMIN']);
    $admin->setPoints(5000);
    $admin->setPassword(
      $this->userPasswordHasher->hashPassword($admin, 'admin123')
    );
    $manager->persist($admin);

    for ($i = 1; $i <= 5; $i++) {
      $user = new User();
      $user->setEmail("user{$i}@example.com");
      $user->setNom("Nom{$i}");
      $user->setPrenom("Prenom{$i}");
      $user->setPoints(rand(100, 2000));
      $user->setPassword(
        $this->userPasswordHasher->hashPassword($user, 'user123')
      );
      $manager->persist($user);
      $users[] = $user; // Stocker pour les notifications
    }

    $categories = ['Électronique', 'Vêtements', 'Livres', 'Sport', 'Maison'];

    for ($i = 1; $i <= 10; $i++) {
      $produit = new Produit();
      $produit->setNom("Produit {$i}");
      $produit->setPrix(rand(50, 500));
      $produit->setCategory($categories[array_rand($categories)]);
      $produit->setDescription("Description du produit {$i}");
      $produit->setCreatedBy($admin);
      $manager->persist($produit);
    }

    // Créer des notifications de test
    // Notifications pour l'admin
    $notification1 = new Notification();
    $notification1->setLabel("Achat effectué par {$users[0]->getPrenom()} {$users[0]->getNom()} - Produit: Produit 1 le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'));
    $notification1->setType(Notification::TYPE_ACHAT);
    $notification1->setUser($admin);
    $notification1->setConcernedUser($users[0]);
    $manager->persist($notification1);

    $notification2 = new Notification();
    $notification2->setLabel("Ajout d'un produit: Produit Test par {$users[1]->getPrenom()} {$users[1]->getNom()} le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'));
    $notification2->setType(Notification::TYPE_PRODUCT);
    $notification2->setUser($admin);
    $notification2->setConcernedUser($users[1]);
    $manager->persist($notification2);

    // Notification pour un utilisateur (points bonus)
    $notification3 = new Notification();
    $notification3->setLabel("Vous avez reçu 1000 points bonus le " . (new \DateTimeImmutable())->format('d/m/Y à H:i'));
    $notification3->setType(Notification::TYPE_POINTS);
    $notification3->setUser($users[0]);
    $notification3->setConcernedUser($users[0]);
    $manager->persist($notification3);

    $manager->flush();
  }
}
