<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
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

    public function createAndFlush(string $title, string $text, int $categoryId, UserInterface $user): void
    {
        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $user]);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $note = new Note($title, $text, $category, $user);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($note);
        foreach ($errors as $error) {
            throw new HttpException(400, $error->getMessage());
        }

        $this->em->persist($note);
        $this->em->flush();
    }
}
