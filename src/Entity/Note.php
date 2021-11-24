<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Assert\NotBlank(message="Note title should not be blank")
     * @Assert\Length(
     *      min = 3,
     *      max = 30,
     *      minMessage = "Note title should be at least {{ limit }} characters long",
     *      maxMessage = "Note title cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=100)
     */
    private string $title;

    /**
     * @Assert\NotBlank(message="Note text name should not be blank")
     * @Assert\Length(
     *      min = 30,
     *      max = 254,
     *      minMessage = "Note text should be at least {{ limit }} characters long",
     *      maxMessage = "Note text cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="text")
     */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private Category $category;

    public function __construct(string $title, string $text, Category $category)
    {
        $this->title = $title;
        $this->text = $text;
        $this->category = $category;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
