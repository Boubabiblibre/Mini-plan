<?php

namespace App\Entity;

use App\Repository\SubscriptionTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionTagRepository::class)]
class SubscriptionTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $tag_id = null;

    #[ORM\Column]
    private ?int $subscription_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTagId(): ?int
    {
        return $this->tag_id;
    }

    public function setTagId(int $tag_id): static
    {
        $this->tag_id = $tag_id;

        return $this;
    }

    public function getSubscriptionId(): ?int
    {
        return $this->subscription_id;
    }

    public function setSubscriptionId(int $subscription_id): static
    {
        $this->subscription_id = $subscription_id;

        return $this;
    }
}
