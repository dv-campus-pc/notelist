<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Enum\FlashMessagesEnum;
use App\Form\NoteType;
use App\Service\NoteService;
use App\Service\PaginationService;
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
    private PaginationService $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    /**
     * @Route(name="list_all")
     */
    public function listAll(EntityManagerInterface $em, Request $request): Response
    {
        $data = $this->paginationService->paginator(
            $em->getRepository(Note::class)->selectByUser($this->getUser()),
            $request
        );

        return $this->render('notelist/list.html.twig', [
            'notes' => $data,
            'lastPage' => $this->paginationService->lastPage($data),
        ]);
    }

    /**
     * @Route("/category/{id}", name="list_by_category", requirements={"categoryId"="\d+"})
     *
     * @IsGranted("IS_OWNER", subject="category", statusCode=404)
     */
    public function listByCategory(Category $category, EntityManagerInterface $em, Request $request): Response
    {
        $data = $this->paginationService->paginator(
            $em->getRepository(Note::class)->selectByCategoryAndUser($category, $this->getUser()),
            $request,
            2
        );
        return $this->render('notelist/list.html.twig', [
            'notes' => $data,
            'lastPage' => $this->paginationService->lastPage($data),
        ]);
    }

    /**
     * @Route("/{id}", name="get", requirements={"id"="\d+"})
     *
     * @IsGranted("IS_SHARED", subject="note", statusCode=404)
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
    public function createAction(Request $request, EntityManagerInterface $em): Response
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

        return $this->renderForm('notelist/create.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     *
     * @IsGranted("IS_SHARED", subject="note", statusCode=404)
     */
    public function deleteAction(Note $note, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $note->getUser()) {
            $entityManager->remove($note);
        } else {
            $note->getUsers()->removeElement($this->getUser());
        }
        $entityManager->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was deleted', $note->getTitle()));

        return $this->redirectToRoute('notelist_list_all');
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Note $note, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();
            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Note "%s" was successfully changed', $note->getTitle()));

            return $this->redirectToRoute('notelist_get', ['id' => $note->getId()]);
        }

        return $this->renderForm('notelist/edit.html.twig', [
            'form' => $form,
            'note' => $note,
        ]);
    }
}
