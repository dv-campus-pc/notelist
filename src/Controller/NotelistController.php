<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notelist", name="notelist_")
 */
class NotelistController extends AbstractController
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
            'notes' => $em->getRepository(Note::class)->findAll()
        ]);
    }

    /**
     * @Route("/{categoryId}", name="list_by_category", requirements={"categoryId"="\d+"})
     */
    public function listByCategory(string $categoryId, EntityManagerInterface $em): Response
    {
        $notes = $em->getRepository(Note::class)->findBy([
            'category' => (int) $categoryId
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
            'id' => $noteId
        ]);

        return $this->render('notelist/get.html.twig', [
            'note' => $note
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $newNote = new Note();
        $newNote
            ->setTitle('New note title')
            ->setText('New note text');

        $entityManager->persist($newNote);
        $entityManager->flush();

        $notes = $this->noteRepository->findAll();

        return $this->render('notelist/list.html.twig', [
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteAction(int $id, EntityManagerInterface $entityManager): Response
    {
        $noteToDelete = $this->noteRepository->find($id);
        if (!$noteToDelete) {
            throw new NotFoundHttpException('Note not found');
        }

        $entityManager->remove($noteToDelete);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Note "%s" was deleted', $noteToDelete->getTitle()));

        return $this->redirectToRoute('notelist_list_all');
    }
}
