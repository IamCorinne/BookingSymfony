<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * permet d'avoir la config de base d'un champs de formulaire
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */

     //on crée une fonction privée car uniquement utilisable dans cette class pour éviter de répéter le label et attribut de $builder + options en 3° paramètre
    //private function getConfiguration($label,$placeholder,$options=[])
    //on utilisera suite à refactorisation le protected au lie de private
    protected function getConfiguration($label,$placeholder,$options=[])
    {
        // retourne donc un tableau de données (on rajoute array_merge pour fusionner deux tableau le premier avec les annotations@ et paramètre de la () de la fonction et le 3° param qui est lui aussi un tableau)
        return array_merge_recursive([
            'label'=>$label,
            'attr'=>['placeholder'=>$placeholder]
            ],
            $options);
    }

}