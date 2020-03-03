<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

//avant : mais on a refactorisé la fonction private function getConfiguration qui était utilisée plusieurs fois du coup on a intégré la class AbstractType dans la  class ApplicationType, ainsi on appelle les 2 class en appelant cette dernière 
//avant =>class AnnonceType extends AbstractType
class RegistrationType extends ApplicationType
{
    /* ca aussi c'est refactorisé dans Applicationtype 
     * permet d'avoir la config de base d'un champs de formulaire
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */

     //avant => on crée une fonction privée car uniquement utilisable dans cette class pour éviter de répéter le label et attribut de $builder + options en 3° paramètre
    /* private function getConfiguration($label,$placeholder,$options=[])
    {
        // retourne donc un tableau de données (on rajoute array_merge pour fusionner deux tableau le premier avec les annotations@ et paramètre de la () de la fonction et le 3° param qui est lui aussi un tableau)
        return array_merge([
            'label'=>$label,
            'attr'=>['placeholder'=>$placeholder]
            ],
            $options);
    } */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class,$this->getConfiguration("Nom","Votre nom"))
            ->add('lastname',TextType::class,$this->getConfiguration("Prénom","Votre prénom"))
            ->add('email',EmailType::class,$this->getConfiguration("Email","Votre email"))
            ->add('hash',PasswordType::class,$this->getConfiguration("Mot de passe","Votre mot de passe"))
            ->add('passwordConfirm',PasswordType::class,$this->getConfiguration("Confirmation de votre mdp","Confirmer votre mot de passe"))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction","Parler de vous"))
            ->add('description',TextareaType::class,$this->getConfiguration("Description","Votre description"))
            ->add('avatar',UrlType::class,$this->getConfiguration("Avatar","URL de votre Avatar"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
