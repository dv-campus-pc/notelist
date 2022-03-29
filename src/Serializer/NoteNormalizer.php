<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Category;
use App\Entity\Note;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class NoteNormalizer implements ContextAwareDenormalizerInterface
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Note::class;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): Note
    {
        $title = $data['title'] ?? '';
        $text = $data['text'] ?? '';

        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof UserInterface) {
            throw new LogicException('To create note User should be authenticated');
        }

        $categoryId = $data['category']['id'] ?? null;
        /** @var Category|null $category */
        $category = $categoryId
            ? $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $user])
            : null;

        if (!$category) {
            throw new ValidationException('Missed category');
        }

        return new Note($title, $text, $category);
    }
}
