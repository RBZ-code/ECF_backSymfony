<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Utilisateur1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options,): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                    'choices' => [
                        '1 heure' => 1,
                        '2 heure' => 2,
                        '3 heure' => 3,
                        '4 heure' => 4,             
                    ]
                ])
            ->add('firstName')
            ->add('lastName')
            ->add('dateOfBirth', null, [
                'widget' => 'single_text'
            ])
            ->add('city')
            ->add('postalCode')
            ->add('phoneNumber')
            ->add('country')
            ->add('street')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}