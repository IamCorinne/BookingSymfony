<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
//pour la validation des champs en annotations champs par champs
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\GreaterThan("now",message="La date d'arrivée ne peut pas être aujourd'hui.",groups="front")
     * @Assert\LessThan("+1 years", message="Les réservations ne peuvent pas se faire plus d'un an en avance. Merci de votre compréhension.")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\GreaterThan(propertyPath="startDate",message="La date de départ doit être après votre date d'arrivée.")
     * 
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;



    /**
     * Callback pour chaque réservation
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return Response
     */
    public function prePersist()
    {
        if(empty($this->createdAt))
        {
            $this->createdAt = new \DateTime();
        }
        if(empty($this->amount))
        {
            $this->amount = $this->ad->getPrice()*$this->getDuration();
        }
    }

    //récupérer les date et verif si les dates sont elles compatibles, réservables
    public function isBookableDays()
    {
        //les dates sont elles dispo (dans ad.php)
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //les dates sont elles en cours de résa (fonction plus bas)
        $bookingDays=$this->getDays();
        // pour la comparaison on met on même format
        $notAvailableDays = array_map(function($day)
        {
            return $day->format('Y-m-d');
        },$notAvailableDays);
        $days = array_map(function($day)
        {
            return $day->format('Y-m-d');
        },$bookingDays);
        //on retourne ok or not
        foreach($days as $day)
        {
            if(array_search($day,$notAvailableDays)!==false)
            return false;
        }
        return true;

    }

    //récup les jours au même format
    public function getDays()
    {
        $resultat = range (
                    $this->getStartDate()->getTimestamp(),
                    $this->getEndDate()->getTimestamp(),
                    24*60*60
                );
        //map modifie le format        
        $days=array_map(function($dayTimestamp)
                {
                    return new \DateTime(date('Y-m-d',$dayTimestamp));
                },$resultat);
        //on retourne 
        return $days;        
    }

    
     //fonction pour calcule nbr jour du sejour
    public function getDuration()
    {
        $difference=$this->endDate->diff($this->startDate);
        return $difference->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
