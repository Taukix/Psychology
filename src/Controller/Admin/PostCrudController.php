<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Repository\UsersRepository;
use DateTime;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')
                ->setLabel('Titre')
                ->setRequired(true),
            TextField::new('description')
                ->setLabel('Description')
                ->setRequired(true),
            ChoiceField::new('state')
                ->setLabel('État')
                ->setRequired(true)
                ->setChoices([
                    'Validé' => 'Validé',
                    'Annulé' => 'Annulé',
                ])
                ->allowMultipleChoices(false),
            AssociationField::new('post_user')
                ->setLabel('Utilisateur')
                ->setFormTypeOptions([
                    'query_builder' => function (UsersRepository $usersRepository) {
                        return $usersRepository->createQueryBuilder('u')
                            ->orderBy('u.email', 'ASC');
                    },
                    'choice_label' => 'email',
                ])
                ->setRequired(true),
            DateTimeField::new('created_at')
                ->setLabel('Date de création')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'data' => new DateTimeImmutable('now'),
                ]),
            DateTimeField::new('modified_at')
                ->setLabel('Date de modification')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'data' => new DateTime('now'),
                ]),
        ];
    }
}
