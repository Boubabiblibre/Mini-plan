<?php

namespace App\Entity;

use App\Repository\SubscriptionHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionHistoryRepository::class)]
class SubscriptionHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $subscriptio_id = null;

    #[ORM\Column(length: 255)]
    private ?string $event_type = null;

    #[ORM\Column(nullable: true)]
    private ?array $old_value = null;

    #[ORM\Column(nullable: true)]
    private ?array $new_value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriptioId(): ?int
    {
        return $this->subscriptio_id;
    }

    public function setSubscriptioId(int $subscriptio_id): static
    {
        $this->subscriptio_id = $subscriptio_id;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->event_type;
    }

    public function setEventType(string $event_type): static
    {
        $this->event_type = $event_type;

        return $this;
    }

    public function getOldValue(): ?array
    {
        return $this->old_value;
    }

    public function setOldValue(?array $old_value): static
    {
        $this->old_value = $old_value;

        return $this;
    }

    public function getNewValue(): ?array
    {
        return $this->new_value;
    }

    public function setNewValue(?array $new_value): static
    {
        $this->new_value = $new_value;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
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
