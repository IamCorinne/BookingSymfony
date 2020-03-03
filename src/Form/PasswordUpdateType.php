<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword',PasswordType::class,$this->getConfiguration("Votre mot de passe","Saisissez votre mdp actuel"))
            ->add('newPassword',PasswordType::class,$this->getConfiguration("Nouveau mot de passe","Saisissez votre nouveau mdp"))
            ->add('confirmPassword',PasswordType::class,$this->getConfiguration("Confirmation mot de passe","Saisissez votre nouveau mdp"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
