<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Webmozart\Assert\Assert as AssertAssert;

class RendezVousFormType extends AbstractType
{
    private $doctrine;

    public function __construct(PersistenceManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'max' => 200,
                        'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('start', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'required' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'hours' => range(8, 17),
                'minutes' => [0],
                'constraints' => [
                    new Assert\Callback([$this, 'validateRendezVous']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }

    public function validateRendezVous($value, ExecutionContextInterface $context)
    {
        $entityManager = $this->doctrine->getManager();
        $existingRdv = $entityManager->getRepository(RendezVous::class)->findOneBy([
            'start' => $value,
        ]);

        if ($existingRdv) {
            $context->buildViolation('L\'heure est déjà réservée.')->atPath('start')->addViolation();
        } elseif ($value instanceof \DateTime && $value->format('H') === '12') {
            $context->buildViolation('L\'heure ne peut pas être égale à 12h (midi).')->atPath('start')->addViolation();
        } elseif ($value instanceof \DateTime && $value->format('H') < '8') {
            $context->buildViolation('L\'heure ne peut pas être inférieure à 8h.')->atPath('start')->addViolation();
        } elseif ($value instanceof \DateTime && $value->format('H') > '17') {
            $context->buildViolation('L\'heure ne peut pas être supérieure à 17h.')->atPath('start')->addViolation();
        } elseif ($value instanceof \DateTime && $value > new \DateTime('now')) {
            $context->buildViolation('Le rendez-vous ne peut pas être pris avec une date antérieur à maintenant.')->atPath('start')->addViolation();
        }
    }
}
