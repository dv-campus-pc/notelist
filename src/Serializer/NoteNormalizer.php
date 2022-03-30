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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class NoteNormalizer implements ContextAwareDenormalizerInterface
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;
    private ObjectNormalizer $objectNormalizer;

    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        ObjectNormalizer $objectNormalizer
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->objectNormalizer = $objectNormalizer;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Note::class;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): Note
    {
        if ($context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? []) {
            return $this->updateNote($context[AbstractNormalizer::OBJECT_TO_POPULATE], $data);
        }

        $title = $data['title'] ?? '';
        $text = $data['text'] ?? '';

        $category = $this->findCategory($data['category']['id'] ?? null);

        return new Note($title, $text, $category);
    }

    private function updateNote($objectToPopulate, $data): Note
    {
        if (!$objectToPopulate instanceof Note) {
            throw new LogicException('NoteNormalizer can update only Note entity');
        }

        $objectToPopulate = $this->objectNormalizer->denormalize($data, Note::class, null, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate,
            'groups' => ['API_UPDATE']
        ]);

        $categoryId = $data['category']['id'] ?? null;
        if (!$categoryId) {
            return $objectToPopulate;
        }

        $category = $this->findCategory($categoryId);
        $objectToPopulate->setCategory($category);

        return $objectToPopulate;
    }

    private function findCategory(?int $categoryId): ?Category
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof UserInterface) {
            throw new LogicException('To create note User should be authenticated');
        }

        $category = $categoryId
            ? $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $user])
            : null;

        if (!$category) {
            throw new ValidationException('Missed category');
        }

        return $category;
    }
}
