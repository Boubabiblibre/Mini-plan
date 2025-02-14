<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 255)]
    private ?string $subscription_type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $total_paid = null;

    #[ORM\Column(nullable: true)]
    private ?bool $auto_renewal = null;

    #[ORM\Column(length: 255)]
    private ?string $billing_mode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $billing_frequency = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $billing_day = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: "subscriptions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $member = null;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: "subscriptions")]
    private ?Service $service = null;

    #[ORM\OneToMany(mappedBy: "subscription", targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: "subscription", targetEntity: SubscriptionTag::class)]
    private Collection $subscriptionTags;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->subscriptionTags = new ArrayCollection();
    }

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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getTotalPaid(): ?int
    {
        return $this->total_paid;
    }

    public function setTotalPaid(int $total_paid): static
    {
        $this->total_paid = $total_paid;
        return $this;
    }

    public function getAutoRenewal(): ?bool
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

    public function getBillingDay(): ?\DateTimeInterface
    {
        return $this->billing_day;
    }

    public function setBillingDay(?\DateTimeInterface $billing_day): static
    {
        $this->billing_day = $billing_day;
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

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;
        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function getSubscriptionTags(): Collection
    {
        return $this->subscriptionTags;
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
