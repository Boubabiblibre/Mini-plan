<?php

namespace App\Entity;

use App\Repository\NotificationTargetRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: NotificationTargetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class NotificationTarget
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Notification::class, inversedBy: "notificationTargets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notification $notification = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "notificationTargets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $read_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->created_at = $now;
        $this->updated_at = $now;
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

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): static
    {
        $this->notification = $notification;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->read_at;
    }

    public function setReadAt(?\DateTimeImmutable $read_at): static
    {
        $this->read_at = $read_at;
        return $this;
    }

    public function markAsRead(): static
    {
        $this->read_at = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}