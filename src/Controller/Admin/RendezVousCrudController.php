<?php

namespace App\Controller\Admin;

use App\Entity\RendezVous;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use App\Repository\UsersRepository;

class RendezVousCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RendezVous::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')
                ->setLabel('Titre')
                ->setRequired(true),
            TextEditorField::new('description')
                ->setLabel('Description')
                ->setRequired(true),
            AssociationField::new('rdv_user')
                ->setLabel('Utilisateur')
                ->setFormTypeOptions([
                    'query_builder' => function (UsersRepository $usersRepository) {
                        return $usersRepository->createQueryBuilder('u')
                            ->orderBy('u.email', 'ASC');
                    },
                    'choice_label' => 'email', // Remplacez 'email' par la propriété que vous souhaitez utiliser comme représentation textuelle
                ])
                ->setRequired(true),
            ChoiceField::new('state')
            ->setLabel('État')
            ->setChoices([
                'Validé' => 'Validé',
                'Annulé' => 'Annulé',
                'Passé' => 'Passé',
            ])
            ->allowMultipleChoices(false),
            DateTimeField::new('start')
                ->setLabel('Date et heure de début')
                ->setFormat('dd-MM-yyyy HH:mm:ss')
                ->setrequired(true),
            DateTimeField::new('end_datetime')
                ->setLabel('Date et heure de fin')
                ->setFormat('dd-MM-yyyy HH:mm:ss')
                ->setrequired(true),
        ];
    }
}
