<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
//use Doctrine\Persistence\ObjectManager;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher une page pour se connecter
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        //class et outils déjà créés grace à symfony
        $error = $utils->getLastAuthenticationError();
        //récuperer le dernier utilisateur = se souvenir de moi
        $username=$utils->getLastUsername();
        return $this->render('account/login.html.twig',
            [
            "hasError"=>$error!==null,
            "username"=>$username
        ]);
    }
    /**
     * Permet de se déconnecter
     * @Route("/logout",name="account_logout")
     *
     * @return void
     */
    public function logout()
    {
        //tout se passe dans le fichier config/packages/test/security.yaml Merci syfony

    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $user = new User();

        //pour créer le formulaire
        $form=$this->createForm(RegistrationType::class,$user);
        //pour enregistrer
        $form->handleRequest($request);
        //verif les données et les fixer
        if($form->isSubmitted() && $form->isValid())
        {
            //on stocke dans $hash l'encodage du mdp
            $hash = $encoder->encodePassword($user, $user->getHash());
            //on modifie le mdp avec le setter
            $user->setHash($hash);
            //on persiste
            $manager->persist($user);
            $manager->flush();
            //on met  un message flash
            $this->addFlash("success", "Votre compte a bien été créé");
            //si tout ok on redirige
            return $this->redirectToRoute("account_login");
        }
        //pour retourner la page
        return $this->render("account/register.html.twig",['form'=>$form->createView()]);
    }

    /**
     * Visuel du profil utilisateur avec modif possible
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        //avec $user=$this->getUser(); symfony recupere le user connecté
        $user=$this->getUser();
        //on crée le form
        $form=$this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success","Les infos sont bien modifiées.");
            
        }
        //on va à la page
        return $this->render("account/profile.html.twig",['form'=>$form->createView()]);
    }

    /**
     * pour modifier le mdp
     * @Route("/account/password", name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $passwordUpdate=new PasswordUpdate();
        //on ne retient que le user en cours donc $user=$this->getUser();
        $user=$this->getUser();
        $form=$this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
        {
            //verif mdp actuel n'est PAS le bon
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash()))
            {
                //message d'erreur avant : $this->addFlash("warning","Mot de passe actuel est incorrect.");
                $form->get("oldPassword")->addError(new FormError("Le mdp entré est incorrect."));
            }
            else{
            //si c'est le bon on le récupère
            $newPassword = $passwordUpdate->getNewPassword();
            //on le crypte
            $hash=$encoder->encodePassword($user,$newPassword);
            //on le modifie dans le setter
            $user->setHash($hash);
            //on enregistre
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success","Le mdp est bien modifié.");
            //on redirige
            return $this->redirectToRoute('account_profile');
            }
        } 
        return $this->render("account/password.html.twig",['form'=>$form->createView()]);
    }

    /**
     * permet d'afficher la page mon compte
     * @Route("/account",name="account_home")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount()
    {
        return $this->render("user/index.html.twig",['user'=>$this->getUser()]);
    }

    /**
     * permet d'afficher la page avec les réservations du client
     * @Route("/account/bookings",name="account_bookings")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function bookings()
    {
        return $this->render('account/bookings.html.twig');
    }
}
