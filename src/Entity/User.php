<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ApiResource(
  normalizationContext: ['groups' => ['user:read']],
  denormalizationContext: ['groups' => ['user:write']],
  operations: []
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['user:read'])]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  #[Groups(['user:read', 'user:write'])]
  private ?string $email = null;

  #[ORM\Column]
  #[Groups(['user:read'])]
  private array $roles = [];

  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 255)]
  #[Groups(['user:read', 'user:write'])]
  private ?string $nom = null;

  #[ORM\Column(length: 255)]
  #[Groups(['user:read', 'user:write'])]
  private ?string $prenom = null;

  #[ORM\Column]
  #[Groups(['user:read'])]
  private ?int $points = 0;

  #[ORM\Column]
  #[Groups(['user:read'])]
  private bool $actif = true;

  #[ORM\Column]
  #[Groups(['user:read'])]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column]
  #[Groups(['user:read'])]
  private ?\DateTimeImmutable $updatedAt = null;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
  private Collection $notifications;

  #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Produit::class)]
  private Collection $produits;

  public function __construct()
  {
    $this->notifications = new ArrayCollection();
    $this->produits = new ArrayCollection();
    $this->createdAt = new \DateTimeImmutable();
    $this->updatedAt = new \DateTimeImmutable();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  public function getRoles(): array
  {
    $roles = $this->roles;
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  public function eraseCredentials(): void {}

  public function getNom(): ?string
  {
    return $this->nom;
  }

  public function setNom(string $nom): self
  {
    $this->nom = $nom;

    return $this;
  }

  public function getPrenom(): ?string
  {
    return $this->prenom;
  }

  public function setPrenom(string $prenom): self
  {
    $this->prenom = $prenom;

    return $this;
  }

  public function getPoints(): ?int
  {
    return $this->points;
  }

  public function setPoints(int $points): self
  {
    $this->points = $points;

    return $this;
  }

  public function isActif(): bool
  {
    return $this->actif;
  }

  public function setActif(bool $actif): self
  {
    $this->actif = $actif;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  #[ORM\PreUpdate]
  public function onPreUpdate(): void
  {
    $this->updatedAt = new \DateTimeImmutable();
  }

  /**
   * @return Collection<int, Notification>
   */
  public function getNotifications(): Collection
  {
    return $this->notifications;
  }

  public function addNotification(Notification $notification): self
  {
    if (!$this->notifications->contains($notification)) {
      $this->notifications->add($notification);
      $notification->setUser($this);
    }

    return $this;
  }

  public function removeNotification(Notification $notification): self
  {
    if ($this->notifications->removeElement($notification)) {
      if ($notification->getUser() === $this) {
        $notification->setUser(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Produit>
   */
  public function getProduits(): Collection
  {
    return $this->produits;
  }

  public function addProduit(Produit $produit): self
  {
    if (!$this->produits->contains($produit)) {
      $this->produits->add($produit);
      $produit->setCreatedBy($this);
    }

    return $this;
  }

  public function removeProduit(Produit $produit): self
  {
    if ($this->produits->removeElement($produit)) {
      if ($produit->getCreatedBy() === $this) {
        $produit->setCreatedBy(null);
      }
    }

    return $this;
  }
}
