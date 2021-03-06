<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteService
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->em = $em;
    }

    public function createAndFlush(string $title, string $text, int $categoryId): void
    {
        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $user]);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $note = new Note($title, $text, $category);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($note);
        foreach ($errors as $error) {
            throw new ValidationException($error->getMessage());
        }

        $this->em->persist($note);
        $this->em->flush();
    }

    public function editAndFlush(Note $note, string $title, string $text, int $categoryId): void
    {
        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $note->getUser()]);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $note->setTitle($title)
            ->setText($text)
            ->setCategory($category);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($note);
        foreach ($errors as $error) {
            throw new ValidationException($error->getMessage());
        }

        $this->em->persist($note);
        $this->em->flush();
    }
}
