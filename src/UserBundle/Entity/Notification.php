<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="user_notification")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;


    /**
     * @var auteur
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="auteur_id", referencedColumnName="id", nullable=true)
     */
    private $auteur;


    /**
     * @var user
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="notifications", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="text")
     */
    private $texte;


    /**
     * @var annonce
     *
     * @ORM\ManyToOne(targetEntity="AnnonceBundle\Entity\Annonce", cascade={"remove", "persist"})
     * @ORM\JoinColumn(name="annonce_id", referencedColumnName="id", nullable=true)
     */
    private $annonce;


    /**
     * @var reservation
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Reservation", cascade={"remove", "persist"})
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id", nullable=true)
     */
    private $reservation;

    /**
     * @var devis
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Devis", cascade={"remove", "persist"})
     * @ORM\JoinColumn(name="devis_id", referencedColumnName="id", nullable=true)
     */
    private $devis;

    /**
     * @var bool
     *
     * @ORM\Column(name="lue", type="boolean")
     */
    private $lue;


    /**
     * @var bool
     *
     * @ORM\Column(name="supprimer", type="boolean")
     */
    private $supprimer;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text")
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->lue = false;
        $this->supprimer = false;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return Notification
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Set auteur
     *
     * @param \UserBundle\Entity\User $auteur
     *
     * @return Notification
     */
    public function setAuteur(\UserBundle\Entity\User $auteur = null)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \UserBundle\Entity\User
     */
    public function getAuteur()
    {
        return $this->auteur;
    }


    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Notification
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set texte
     *
     * @param string $texte
     *
     * @return Notification
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set lue
     *
     * @param boolean $lue
     *
     * @return Notification
     */
    public function setLue($lue)
    {
        $this->lue = $lue;

        return $this;
    }

    /**
     * Get lue
     *
     * @return bool
     */
    public function getLue()
    {
        return $this->lue;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Notification
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set annonce
     *
     * @param \AnnonceBundle\Entity\Annnonce $annonce
     *
     * @return Notification
     */
    public function setAnnonce(\AnnonceBundle\Entity\Annonce $annonce = null)
    {
        $this->annonce = $annonce;

        return $this;
    }

    /**
     * Get annonce
     *
     * @return \AnnonceBundle\Entity\Annnonce
     */
    public function getAnnonce()
    {
        return $this->annonce;
    }


    /**
     * Set reservation
     *
     * @param \AnnonceBundle\Entity\Reservation $reservation
     *
     * @return Notification
     */
    public function setReservation(\UserBundle\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \AnnonceBundle\Entity\Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }


    /**
     * Set devis
     *
     * @param \UserBundle\Entity\Devis $devis
     *
     * @return Notification
     */
    public function setDevis(\UserBundle\Entity\Devis $devis = null)
    {
        $this->devis = $devis;

        return $this;
    }

    /**
     * Get devis
     *
     * @return \UserBundle\Entity\Devis
     */
    public function getDevis()
    {
        return $this->devis;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set supprimer
     *
     * @param boolean $supprimer
     *
     * @return Notification
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return boolean
     */
    public function getSupprimer()
    {
        return $this->supprimer;
    }

    public function toArray(){
        return array(
            "Id"=>$this->getid(),
            "User"=>$this->getUser()->toArray(),
            "Annonce"=>is_null($this->getAnnonce()) ? false : $this->getAnnonce()->toArray(),
            "Type"=>$this->getType(),
            "Texte"=>$this->getTexte(),
            "CreatedAt"=>$this->getCreatedAt()->getTimestamp()

        );
    }

    

    

    

    
}
