<?php

namespace App\Form;

use App\Entity\Opinion;
use App\Entity\Payment;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_return', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'data-controller' => 'datepicker'
                ]
            ])

            ->add('date_arrived', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'data-controller' => 'datepicker'
                ]
            ])
            ->add('total_price')
            ->add('status')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'id',
            ])
            ->add('payment', EntityType::class, [
                'class' => Payment::class,
                'choice_label' => 'id',
            ])
            ->add('opinion', EntityType::class, [
                'class' => Opinion::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
