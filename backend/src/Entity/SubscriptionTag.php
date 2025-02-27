<?php

namespace App\Entity;

use App\Repository\SubscriptionTagRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: SubscriptionTagRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SubscriptionTag
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(targetEntity: Subscription::class, inversedBy: "subscriptionTags")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subscription $subscription = null;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: "subscriptionTags")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tag $tag = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $read_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
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

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;
        return $this;
    }
}
