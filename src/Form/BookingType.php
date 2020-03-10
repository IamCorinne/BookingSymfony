<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    //transformer les dates en bon bon formats grace à Form/DataTransformer/FrToDatetimeTransformer
    private $transformer;
    public function __construct(FrToDatetimeTransformer $transformer)
    {
        //pour initialiser
        $this->transformer = $transformer;
    }

    //construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class, $this->getConfiguration("Date d'arrivée","Date de votre arrivée"))
            ->add('endDate',TextType::class, $this->getConfiguration("Date de départ","Date de votre départ"))
            ->add('comment',TextareaType::class, $this->getConfiguration(false,"Ajoutez votre commentaire ",['required'=>false]))
        ;
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
