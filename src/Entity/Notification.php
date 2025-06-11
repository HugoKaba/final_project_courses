<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
  normalizationContext: ['groups' => ['notification:read']],
  denormalizationContext: ['groups' => ['notification:write']],
  operations: []
)]
class Notification
{
  public const TYPE_PRODUCT = 'product';
  public const TYPE_BLOCAGE = 'blocage';
  public const TYPE_REACTIVATION = 'reactivation';
  public const TYPE_ACHAT = 'achat';
  public const TYPE_POINTS = 'points';

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['notification:read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['notification:read', 'notification:write'])]
  private ?string $label = null;

  #[ORM\Column(length: 50)]
  #[Groups(['notification:read', 'notification:write'])]
  private ?string $type = null;

  #[ORM\ManyToOne(inversedBy: 'notifications')]
  #[ORM\JoinColumn(nullable: false)]
  #[Groups(['notification:read', 'notification:write'])]
  private ?User $user = null;

  #[ORM\ManyToOne]
  #[Groups(['notification:read'])]
  private ?User $concernedUser = null;

  #[ORM\Column]
  #[Groups(['notification:read'])]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column]
  #[Groups(['notification:read'])]
  private ?\DateTimeImmutable $updatedAt = null;

  public function __construct()
  {
    // Les timestamps seront gérés par TimestampListener
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getLabel(): ?string
  {
    return $this->label;
  }

  public function setLabel(string $label): self
  {
    $this->label = $label;

    return $this;
  }

  public function getType(): ?string
  {
    return $this->type;
  }

  public function setType(string $type): self
  {
    $this->type = $type;

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  public function getConcernedUser(): ?User
  {
    return $this->concernedUser;
  }

  public function setConcernedUser(?User $concernedUser): self
  {
    $this->concernedUser = $concernedUser;

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
}
