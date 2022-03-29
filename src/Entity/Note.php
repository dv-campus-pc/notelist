<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\Ownable;
use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use LogicException;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note implements Ownable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups("API")
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
     *
     * @Groups("API")
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
     *
     * @Groups("API")
     */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups("API")
     */
    private Category $category;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Collection $users;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups("API")
     */
    private UserInterface $owner;

    public function __construct(string $title, string $text, Category $category)
    {
        $this->title = $title;
        $this->text = $text;
        $this->category = $category;

        $this->users = new ArrayCollection();
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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function setOwner(UserInterface $owner): Note
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->getOwner();
    }

    public function setUser(UserInterface $user)
    {
        $this->owner = $user;
        $this->users->add($user);
    }
}
