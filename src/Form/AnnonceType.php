<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends AbstractType
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
    private function getConfiguration($label,$placeholder,$options=[])
    {
        // retourne donc un tableau de données (on rajoute array_merge pour fusionner deux tableau le premier avec les annotations@ et paramètre de la () de la fonction et le 3° param qui est lui aussi un tableau)
        return array_merge([
            'label'=>$label,
            'attr'=>['placeholder'=>$placeholder]
            ],
            $options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',          TextType::class,$this->getConfiguration('Titre','Votre titre'))
            ->add('slug',           TextType::class,$this->getConfiguration('Alias','Votre alias pour personaliser l\'url',['required'=>false]))
            ->add('coverImage',     UrlType::class,$this->getConfiguration('Image de couverture','Votre image'))
            ->add('introduction',   TextType::class,$this->getConfiguration('Résumé','Présentez votre bien'))
            ->add('content',        TextareaType::class,$this->getConfiguration('Description détaillée','Décrivez votre bien'))
            ->add('rooms',          IntegerType::class,$this->getConfiguration('Nombre de chambre','Nb chambre'))
            ->add('price',          MoneyType::class,$this->getConfiguration('Prix','Votre prix/nuit'))
            //entry_type pour répétitions des sous dossiers de la class ImageType (src/form/imagetype.php); allow_add permet d'ajouter
            ->add('images',         CollectionType::class,['entry_type'=>ImageType::class,'allow_add'=>true,'allow_delete'=>true])        
            //->add('save',           SubmitType::class,$this->getConfiguration('Créer !','Créer !'))
            //->getForm()
            ;
            //on vient de créer une class SubmitType on oublie pas de l'importer
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
