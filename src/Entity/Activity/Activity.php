<?php

declare(strict_types=1);

namespace App\Entity\Activity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use App\Repository\ActivityRepository;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"visit" = "VisitActivity", "edit_notelist" = "EditNoteActivity"})
 */
abstract class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    public function __construct(?User $user) {
        $this->user = $user;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
