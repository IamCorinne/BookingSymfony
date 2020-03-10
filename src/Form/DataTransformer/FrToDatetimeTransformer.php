<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrToDatetimeTransformer implements DataTransformerInterface
{
    //pour transformer les données pour pouvoir les afficher dans une formulaires
    public function transform($date)
    {
        if($date===null){return '';}
        //si la date n'est pas null on retourne le format
        return $date->format('d/m/Y');
    }

    // prend la donnée du formulaire et la transforme dans le format attendu
    public function reverseTransform($dateFr)
    {
        //si la date est null on retourne une execption
        if($dateFr===null){throw new TransformationFailedException("Fournir une date.");}
        //si la date n'est pas null on retourne le format
        $date = \Datetime::createFromFormat('d/m/Y',$dateFr);
        //si la date est fausse on retourne une exception
        if($dateFr===false){throw new TransformationFailedException("Le format de cette date n'est pas correcte.");}
        return $date;
    }
}