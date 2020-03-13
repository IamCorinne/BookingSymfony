<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//pour la validation des champs en annotations champs par champs
use Symfony\Component\Validator\Constraints as Assert;
//pour la validation des champs en annotations au global
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"title"},
 * message="Une autre annonce a déjà ce titre, veuillez en changer.")
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min=10,
     * max=255,
     * minMessage="Le titre doit faire plus de 10 caractères.",
     * maxMessage="Votre titre est trop long, max 255 caractères.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     * min=10,
     * minMessage="Cette intro est un peu courte, elle mérite plus de 10 caractères.")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad",orphanRemoval=true)
     */
    //orphanRemoval=true pour supprimer les images qui deviennent orphelines suites à une modif, elles n'appartiennent plus à une annonce
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    /**
     * Création d'une fonction pour initialiser le slug avant la persistance(enregistrement) et la mise à jour
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

     public function initializeSlug()
     {
         if(empty($this->slug))
         {
             $slugify = new Slugify();
             $this->slug = $slugify->slugify($this->title);
         }
     }

    
     /**
      * on récupère Un seul commentaire pour une seule annonce par le booker
      *
      * @param User $author
      * @return Comment|null
      */
     public function getCommentFromAuthor(User $author)
     {
         foreach($this->comments as $comment)
         {
             if($comment->getAuthor() === $author) 
             return $comment;
         }
         return null;
     }

     //calcul de la moyenne globale
     public function getAverageRatings()
     {
         //faire la somme de toutes les notes voir array_reduce ( array $array , callable $callback [, mixed $initial = NULL ] ) : mixed
         $sum = array_reduce($this->comments->toArray(),function($total,$comment)
         {
            //on retourne le total + la note de chaque commentaire
            return $total + $comment ->getRating();
         },0);
         //diviser le tt par le nbr d'avis
         if(count($this->comments)>0)
         return $sum / count($this->comments);
         return 0;


     }

     //fonction pour les dates non réservables
     public function getNotAvailableDays()
     { 
         //creation du tableau vide pour les dates
         $notAvailableDays=[];
         foreach($this->bookings as $booking)
            {
                //$resultat = range(10,20,2); donne [2,10,20]
                //$resultat = range(03-20-2019,03-25-2019); donne [03-20-2019,03-25-2019]
                //on va ranger grace avec timestamp (date depuis 1970)
                $resultat = range (
                    $booking->getStartDate()->getTimestamp(),
                    $booking->getEndDate()->getTimestamp(),
                    24*60*60
                );
                //map modifie le format
                $days= array_map(function($dayTimestamp)
                {
                    return new \DateTime(date('Y-m-d',$dayTimestamp));
                },$resultat);
                //on rempli , on fusionne le tableau
                $notAvailableDays = array_merge($notAvailableDays,$days);
            }
            //on retourne le tableau
            return $notAvailableDays;

     }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
