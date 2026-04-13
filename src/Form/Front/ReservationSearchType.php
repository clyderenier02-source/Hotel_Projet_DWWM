<?php

namespace App\Form\Front;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationSearchType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_arrived', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            
            ->add('date_return', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
        ;
    }
}