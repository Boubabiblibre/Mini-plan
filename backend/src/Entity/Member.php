<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Member
{
    public const RELATION_PARENT = 'parent';
    public const RELATION_CHILD = 'child';
    public const RELATION_PARTNER = 'partner';
    public const RELATION_OTHER = 'other';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $relationship = null;

    #[ORM\ManyToOne(targetEntity: Space::class, inversedBy: "members")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Space $space = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "members")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\OneToMany(mappedBy: "member", targetEntity: Subscription::class)]
    private Collection $subscriptions;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->created_at = $now;
        $this->updated_at = $now;
        $this->subscriptions = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function setRelationship(?string $relationship): static
    {
        if ($relationship !== null && !in_array($relationship, [
            self::RELATION_PARENT, 
            self::RELATION_CHILD, 
            self::RELATION_PARTNER, 
            self::RELATION_OTHER
        ])) {
            throw new \InvalidArgumentException("Relation invalide.");
        }
        $this->relationship = $relationship;
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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setMember($this);
        }
        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            if ($subscription->getMember() === $this) {
                $subscription->setMember(null);
            }
        }
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
}
