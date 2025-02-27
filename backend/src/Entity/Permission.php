<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    public const TYPE_ADMIN = 'ROLE_ADMIN';
    public const TYPE_EDITOR = 'ROLE_EDITOR';
    public const TYPE_VIEWER = 'ROLE_VIEWER';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $permission_type = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $assigned_at = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "permissions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\ManyToOne(targetEntity: Space::class, inversedBy: "permissions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Space $space = null;

    public function __construct()
    {
        $this->assigned_at = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id ? $this->id : null;
    }

    public function getPermissionType(): ?string
    {
        return $this->permission_type;
    }

    public function setPermissionType(string $permission_type): static
    {
        if (!in_array($permission_type, [self::TYPE_ADMIN, self::TYPE_EDITOR, self::TYPE_VIEWER])) {
            throw new \InvalidArgumentException("Type de permission invalide.");
        }
        $this->permission_type = $permission_type;
        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assigned_at;
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

    public function getSpace(): ?Space
    {
        return $this->space;
    }

    public function setSpace(?Space $space): static
    {
        $this->space = $space;
        return $this;
    }
}
