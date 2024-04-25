<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', null, [
                'widget' => 'single_text',
            ])
            // ->add('choixEndDate', ChoiceType::class, [
            //     'choices' => [
            //         '1 heure' => 1,
            //         '2 heure' => 2,
            //         '3 heure' => 3,
            //         '4 heure' => 4,             
            //     ]
            // ])
            ->add('idRoom', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'id',
                'disabled' => true,
            ])
            ->add('User', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
                
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'roomId' => null, 
        ]);
    }
}
