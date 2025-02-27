<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Payment
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public const METHOD_CREDIT_CARD = 'credit_card';
    public const METHOD_PAYPAL = 'paypal';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(length: 255)]
    private ?string $payment_method = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: Subscription::class, inversedBy: "payments")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subscription $subscription = null;

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

    public function getAmount(): ?float
    {
        return $this->amount ? floatval($this->amount) : null;
    }

    public function setAmount(float|string $amount): static
    {
        $this->amount = number_format((float) $amount, 2, '.', '');
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): static
    {
        if (!in_array($payment_method, [self::METHOD_CREDIT_CARD, self::METHOD_PAYPAL, self::METHOD_BANK_TRANSFER])) {
            throw new \InvalidArgumentException("Méthode de paiement invalide.");
        }
        $this->payment_method = $payment_method;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_COMPLETED, self::STATUS_FAILED])) {
            throw new \InvalidArgumentException("Statut de paiement invalide.");
        }
        $this->status = $status;
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

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): static
    {
        $this->subscription = $subscription;
        return $this;
    }
}
