<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                new Length([
                    'min' => 10,
                    'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                    'max' => 50,
                    'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères',
                ]),
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                new Length([
                    'min' => 10,
                    'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                    'max' => 100,
                    'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères',
                ]),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
