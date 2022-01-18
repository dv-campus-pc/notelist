<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Enum\FlashMessagesEnum;
use App\Form\NoteType;
use App\Service\NoteService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notelist", name="notelist_")
 *
 * @IsGranted("ROLE_USER")
 */
class NoteController extends AbstractController
{
    /**
     * @Route(name="list_all")
     */
    public function listAll(EntityManagerInterface $em): Response
    {
        return $this->render('notelist/list.html.twig', [
            'notes' => $em->getRepository(Note::class)->findByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/category/{id}", name="list_by_category", requirements={"categoryId"="\d+"})
     *
     * @IsGranted("IS_OWNER", subject="category", statusCode=404)
     */
    public function listByCategory(Category $category, EntityManagerInterface $em): Response
    {
        $notes = $em->getRepository(Note::class)->findByCategoryAndUser($category, $this->getUser());

        return $this->render('notelist/list.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/{id}", name="get", requirements={"id"="\d+"})
     *
     * @IsGranted("IS_OWNER", subject="note", statusCode=404)
     */
    public function getAction(Note $note): Response
    {
        return $this->render('notelist/get.html.twig', [
            'note' => $note
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em, NoteService $noteService): Response
    {
        if ($request->getMethod() === 'GET') {
            $categories = $em->getRepository(Category::class)->findBy(['user' => $this->getUser()]);

            return $this->render('notelist/create.html.twig', [
                'categories' => $categories
            ]);
        }

        $title = (string) $request->request->get('title');
        $noteService->createAndFlush(
            $title,
            (string) $request->request->get('text'),
            (int) $request->request->get('category_id')
        );
        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was created', $title));

        return $this->redirectToRoute('notelist_create');
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NoteType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note = $form->getData();
            $em->persist($note);
            $em->flush();
            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was successfully created', $note->getTitle()));

            return $this->redirectToRoute('notelist_list_all');
        }

        return $this->renderForm('notelist/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     *
     * @IsGranted("IS_OWNER", subject="note", statusCode=404)
     */
    public function deleteAction(Note $note, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($note);
        $entityManager->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was deleted', $note->getTitle()));

        return $this->redirectToRoute('notelist_list_all');
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Note $note, EntityManagerInterface $em, NoteService $noteService): Response
    {
        if ($request->getMethod() === 'GET') {
            $categories = $em->getRepository(Category::class)->findBy(['user' => $this->getUser()]);

            return $this->render('notelist/edit.html.twig', [
                'note' => $note,
                'categories' => $categories
            ]);
        }

        $title = (string) $request->request->get('title');
        $noteService->editAndFlush(
            $note,
            $title,
            (string) $request->request->get('text'),
            (int) $request->request->get('category_id')
        );
        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was edited', $title));

        return $this->redirectToRoute('notelist_get', ['id' => $note->getId()]);
    }
}
