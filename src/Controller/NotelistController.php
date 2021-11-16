<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->getMethod() === 'GET') {
            $categories = $em->getRepository(Category::class)->findAll();

            return $this->render('notelist/create.html.twig', [
                'categories' => $categories
            ]);
        }

        // TODO: add data validation
        $title = (string) $request->request->get('title');
        $text = (string) $request->request->get('text');

        $categoryId = (int) $request->request->get('category_id');
        $category = $em->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $note = new Note($title, $text, $category);
        $em->persist($note);
        $em->flush();

        $this->addFlash('success', sprintf('Note "%s" was created', $note->getTitle()));

        return $this->redirectToRoute('notelist_create');
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
