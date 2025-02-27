<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    public const TYPE_INFO = 'info';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $notification_type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sent_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "notifications")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Users $sender = null;

    #[ORM\ManyToOne(targetEntity: Space::class, inversedBy: "notifications")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Space $space = null;

    #[ORM\OneToMany(mappedBy: "notification", targetEntity: NotificationTarget::class)]
    private Collection $notificationTargets;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->created_at = $now;
        $this->updated_at = $now;
        $this->sent_at = $now;
        $this->notificationTargets = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id ? $this->id : null;
    }

    public function getNotificationType(): ?string
    {
        return $this->notification_type;
    }

    public function setNotificationType(string $notification_type): static
    {
        if (!in_array($notification_type, [self::TYPE_INFO, self::TYPE_WARNING, self::TYPE_ERROR])) {
            throw new \InvalidArgumentException("Type de notification invalide.");
        }
        $this->notification_type = $notification_type;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sent_at;
    }

    public function markAsSent(): static
    {
        $this->sent_at = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): static
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSpace(): ?Space
    {
        return $this->space;
    }

    public function setSpace(?Space $space): static
    {
        $this->space = $space;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function getNotificationTargets(): Collection
    {
        return $this->notificationTargets;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
