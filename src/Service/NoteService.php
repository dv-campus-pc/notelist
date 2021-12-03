<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Enum\FlashMessagesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteService
{
    private ValidatorInterface $validator;
    private Session $session;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        SessionInterface $session,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->session = $session;
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
            $this->session->getFlashBag()->add(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if (!$errors->count()) {
            $this->em->persist($note);
            $this->em->flush();

            $this->session->getFlashBag()->add(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was created', $note->getTitle()));
        }
    }
}
