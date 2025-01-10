<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 255)]
    private ?string $subscription_type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(nullable: true)]
    private ?bool $auto_renewal = null;

    #[ORM\Column(length: 255)]
    private ?string $billing_mode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $billing_frequency = null;

    #[ORM\Column(nullable: true)]
    private ?int $tag_id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $member_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $service_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getSubscriptionType(): ?string
    {
        return $this->subscription_type;
    }

    public function setSubscriptionType(string $subscription_type): static
    {
        $this->subscription_type = $subscription_type;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function isAutoRenewal(): ?bool
    {
        return $this->auto_renewal;
    }

    public function setAutoRenewal(?bool $auto_renewal): static
    {
        $this->auto_renewal = $auto_renewal;

        return $this;
    }

    public function getBillingMode(): ?string
    {
        return $this->billing_mode;
    }

    public function setBillingMode(string $billing_mode): static
    {
        $this->billing_mode = $billing_mode;

        return $this;
    }

    public function getBillingFrequency(): ?string
    {
        return $this->billing_frequency;
    }

    public function setBillingFrequency(?string $billing_frequency): static
    {
        $this->billing_frequency = $billing_frequency;

        return $this;
    }

    public function getTagId(): ?int
    {
        return $this->tag_id;
    }

    public function setTagId(?int $tag_id): static
    {
        $this->tag_id = $tag_id;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMemberId(): ?int
    {
        return $this->member_id;
    }

    public function setMemberId(int $member_id): static
    {
        $this->member_id = $member_id;

        return $this;
    }

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function setServiceId(?int $service_id): static
    {
        $this->service_id = $service_id;

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
