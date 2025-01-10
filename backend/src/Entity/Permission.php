<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $permission_type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $assigned_at = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $space_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermissionType(): ?string
    {
        return $this->permission_type;
    }

    public function setPermissionType(string $permission_type): static
    {
        $this->permission_type = $permission_type;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assigned_at;
    }

    public function setAssignedAt(\DateTimeImmutable $assigned_at): static
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getSpaceId(): ?int
    {
        return $this->space_id;
    }

    public function setSpaceId(int $space_id): static
    {
        $this->space_id = $space_id;

        return $this;
    }
}
