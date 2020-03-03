<?php

namespace App\Entity;

//pour la validation des champs en annotations champs par champs
use Symfony\Component\Validator\Constraints as Assert;

//on enlève toutes les annotations car on ne veut pas de lien  avec la BDD
class PasswordUpdate
{
    

    private $oldPassword;

    /**
     * @Assert\Length(min=8,minMessage="Votre mdp doit comporter 8 caractères au minimun.")
     */
    private $newPassword;

     /**
     * @Assert\EqualTo(propertyPath="newPassword",message="les deux mots de passes ne sont pas identiques.")
     */
    private $confirmPassword;

    

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
