<?php

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactMessage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname')
                ->setLabel('Prénom')
                ->setRequired(true),
            TextField::new('lastname')
                ->setLabel('Nom')
                ->setRequired(true),
            TextField::new('email')
                ->setLabel('Email')
                ->setRequired(true),
            TextField::new('subject')
                ->setLabel('Objet')
                ->setRequired(true),
            TextareaField::new('message')
                ->setLabel('Message')
                ->setRequired(true),
        ];
    }
}
