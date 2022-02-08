<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NoteType extends AbstractType
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('text')
            ->add('category', null, [
                'choice_label' => 'title',
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    return $categoryRepository->selectByUser($this->getUser());
                },
            ])
            ->add('users', null, [
                'choice_label' => 'username',
                'label' => 'Shared to users',
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'empty_data' => static function (FormInterface $form) {
                return new Note(
                    $form->get('title')->getData(),
                    $form->get('text')->getData(),
                    $form->get('category')->getData()
                );
            },
        ]);
    }

    private function getUser(): ?User
    {
        return $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
    }

    public function onPreSetData(PreSetDataEvent $event)
    {
        $form =  $event->getForm();

        if (!$this->getUser()->hasRole(RolesEnum::ADMIN)) {
            $form->remove('users');
        }

        /** @var Note $data */
        if (!$data = $event->getData()) {
            return;
        }

        if ($data->getOwner() !== $this->getUser()) {
            $form->remove('category');
        }
    }
}
