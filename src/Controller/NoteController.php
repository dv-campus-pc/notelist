<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Enum\FlashMessagesEnum;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/notelist", name="notelist_")
 */
class NoteController extends AbstractController
{
    private NoteRepository $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route(name="list_all")
     */
    public function listAll(EntityManagerInterface $em): Response
    {
        return $this->render('notelist/list.html.twig', [
            'notes' => $em->getRepository(Note::class)->findBy(['user' => $this->getUser()])
        ]);
    }

    /**
     * @Route("/{categoryId}", name="list_by_category", requirements={"categoryId"="\d+"})
     */
    public function listByCategory(string $categoryId, EntityManagerInterface $em): Response
    {
        $notes = $em->getRepository(Note::class)->findBy([
            'category' => (int) $categoryId,
            'user' => $this->getUser()
        ]);

        return $this->render('notelist/list.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/{categoryId}/{noteId}", name="get", requirements={"categoryId"="\d+", "noteId"="\d+"})
     */
    public function getAction(string $categoryId, string $noteId, EntityManagerInterface $em): Response
    {
        $note = $em->getRepository(Note::class)->findOneBy([
            'category' => (int) $categoryId,
            'id' => $noteId,
            'user' => $this->getUser()
        ]);

        return $this->render('notelist/get.html.twig', [
            'note' => $note
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        if ($request->getMethod() === 'GET') {
            $categories = $em->getRepository(Category::class)->findBy(['user' => $this->getUser()]);

            return $this->render('notelist/create.html.twig', [
                'categories' => $categories
            ]);
        }

        $title = (string) $request->request->get('title');
        $text = (string) $request->request->get('text');

        $categoryId = (int) $request->request->get('category_id');
        $category = $em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $this->getUser()]);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $note = new Note($title, $text, $category, $this->getUser());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($note);
        foreach ($errors as $error) {
            $this->addFlash(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if (!$errors->count()) {
            $em->persist($note);
            $em->flush();

            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was created', $note->getTitle()));
        }

        return $this->redirectToRoute('notelist_create');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteAction(int $id, EntityManagerInterface $entityManager): Response
    {
        $noteToDelete = $this->noteRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if (!$noteToDelete) {
            throw new NotFoundHttpException('Note not found');
        }

        $entityManager->remove($noteToDelete);
        $entityManager->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was deleted', $noteToDelete->getTitle()));

        return $this->redirectToRoute('notelist_list_all');
    }
}
