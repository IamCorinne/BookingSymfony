<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',  DateType::class,['label'=>'Début du séjour'])
            ->add('endDate',    DateType::class,['label'=>'Fin du séjour'])
            ->add('comment',    TextareaType::class,['label'=>'Commentaire client'])
            ->add('booker',     EntityType::class,[ 'class'=>User::class,
                                                    'choice_label'=>function($user){return $user->getFirstname()." ".strtoupper($user->getLastname());},
                                                    'label'=>'Visiteur'])
            ->add('ad',         EntityType::class,['class'=>Ad::class,
                                                    'choice_label'=>function($ad){return $ad->getId()." - ".$ad->getTitle();},
                                                    'label'=>'Annonce'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups'=>['Default']
        ]);
    }
}
