<?php

namespace AnnonceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Annonce
 *
 * @ORM\Table(name="annonce")
 * @ORM\Entity(repositoryClass="AnnonceBundle\Repository\AnnonceRepository")
 */
class Annonce
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
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User", inversedBy="annonces",cascade={"persist"}))
     * @ORM\JoinColumn(name="auteur_id", referencedColumnName="id")
     */
    private $auteur;

    /**
     * @var taxi
     *
     * @ORM\OneToOne(targetEntity="\AnnonceBundle\Entity\Taxi", cascade={"remove", "persist"},inversedBy="annonce")
     * @ORM\JoinColumn(name="taxi_id", referencedColumnName="id")
     */
    private $taxi;

    /**
     * @var covoiturage
     *
     * @ORM\OneToOne(targetEntity="\AnnonceBundle\Entity\covoiturage", cascade={"remove", "persist"},inversedBy="annonce")
     * @ORM\JoinColumn(name="covoiturage_id", referencedColumnName="id")
     */
    private $covoiturage;

    /**
     * @var vtc
     *
     * @ORM\OneToOne(targetEntity="\AnnonceBundle\Entity\vtc", cascade={"remove", "persist"},inversedBy="annonce")
     * @ORM\JoinColumn(name="vtc_id", referencedColumnName="id")
     */
    private $vtc;

    /**
     * @var demenagement
     *
     * @ORM\OneToOne(targetEntity="\AnnonceBundle\Entity\Demenagement", cascade={"remove", "persist"},inversedBy="annonce")
     * @ORM\JoinColumn(name="demenagement_id", referencedColumnName="id")
     */
    private $demenagement;


    /**
     * @var reservations
     *
     * @ORM\OneToMany(targetEntity="\UserBundle\Entity\reservation", mappedBy="annonce", cascade={"remove", "persist"})
     */
    private $reservations;


    /**
     * @var notifications
     *
     * @ORM\OneToMany(targetEntity="\UserBundle\Entity\Notification", mappedBy="annonce", cascade={"remove", "persist"})
     */
    private $notifications;


    /**
     * @var avis
     *
     * @ORM\OneToMany(targetEntity="\UserBundle\Entity\Avis", mappedBy="annonce", cascade={"remove", "persist"})
     */
    private $avis;


    /**
     * @var \string
     *
     * @ORM\Column(name="categorie", type="string", nullable=true)
     */
    private $categorie;


    /**
     * @var \integer
     *
     * @ORM\Column(name="rayon", type="integer", nullable=true)
     */
    private $rayon;

    /**
     * @var \place
     *
     * @ORM\Column(name="place", type="array", nullable=true)
     */
    private $place;


    /**
     * @var boolean
     *
     * @ORM\Column(name="terminer", type="boolean")
     */
    private $terminer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiredAt", type="datetime")
     */
    private $expiredAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->expiredAt = new \DateTime();
        $this->reservations = new ArrayCollection();
        $this->terminer = false;
        $this->actif = false;
    }

    public function updateEntity(){
        $this->updatedAt = new \DateTime();
        $this->expiredAt = new \DateTime();
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
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Annonce
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set taxi
     *
     * @param string $taxi
     *
     * @return Annonce
     */
    public function setTaxi($taxi)
    {
        $this->taxi = $taxi;

        return $this;
    }

    /**
     * Get taxi
     *
     * @return string
     */
    public function getTaxi()
    {
        return $this->taxi;
    }

    /**
     * Set covoiturage
     *
     * @param integer $covoiturage
     *
     * @return Annonce
     */
    public function setCovoiturage($covoiturage)
    {
        $this->covoiturage = $covoiturage;

        return $this;
    }

    /**
     * Get covoiturage
     *
     * @return int
     */
    public function getCovoiturage()
    {
        return $this->covoiturage;
    }

    /**
     * Set vtc
     *
     * @param integer $vtc
     *
     * @return Annonce
     */
    public function setVtc($vtc)
    {
        $this->vtc = $vtc;

        return $this;
    }

    /**
     * Get vtc
     *
     * @return int
     */
    public function getVtc()
    {
        return $this->vtc;
    }

    /**
     * Set demenagement
     *
     * @param integer $demenagement
     *
     * @return Annonce
     */
    public function setDemenagement($demenagement)
    {
        $this->demenagement = $demenagement;

        return $this;
    }

    /**
     * Get demenagement
     *
     * @return int
     */
    public function getDemenagement()
    {
        return $this->demenagement;
    }



    /**
     * Set createdAt
     *
     * @param datetime $demenagement
     *
     * @return Annonce
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }



    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     *
     * @return Annonce
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Add reservation
     *
     * @param \UserBundle\Entity\reservation $reservation
     *
     * @return Annonce
     */
    public function addReservation(\UserBundle\Entity\reservation $reservation)
    {
        $this->reservations->add($reservation);

        return $this;
    }


    /**
     * Set reservations
     *
     * @param \UserBundle\Entity\reservations $reservations
     *
     * @return Annonce
     */
    public function setReservations(\UserBundle\Entity\reservation $reservations = null)
    {
        $this->reservations = $reservations;

        return $this;
    }

    /**
     * Get reservations
     *
     * @return \UserBundle\Entity\reservations
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Annonce
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set rayon
     *
     * @param integer $rayon
     *
     * @return Annonce
     */
    public function setRayon($rayon)
    {
        $this->rayon = $rayon;

        return $this;
    }

    /**
     * Get rayon
     *
     * @return integer
     */
    public function getRayon()
    {
        return $this->rayon;
    }



    /**
     * Set place
     *
     * @param array $place
     *
     * @return Annonce
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return array
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set avis
     *
     * @param \UserBundle\Entity\Avis $avis
     *
     * @return Annonce
     */
    public function setAvis(\UserBundle\Entity\Avis $avis = null)
    {
        $this->avis = $avis;

        return $this;
    }

    /**
     * Get avis
     *
     * @return \UserBundle\Entity\Avis
     */
    public function getAvis()
    {
        return $this->avis;
    }


    /**
     * Set terminer
     *
     * @param boolean $terminer
     *
     * @return Annonce
     */
    public function setTerminer($terminer)
    {
        $this->terminer = $terminer;

        return $this;
    }

    /**
     * Get terminer
     *
     * @return boolean
     */
    public function getTerminer()
    {
        return $this->terminer;
    }

    /**
     * Set expiredAt
     *
     * @param \DateTime $expiredAt
     *
     * @return Annonce
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get expiredAt
     *
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * Add avi
     *
     * @param \UserBundle\Entity\Avis $avi
     *
     * @return Annonce
     */
    public function addAvi(\UserBundle\Entity\Avis $avi)
    {
        $this->avis[] = $avi;

        return $this;
    }

    /**
     * Remove avi
     *
     * @param \UserBundle\Entity\Avis $avi
     */
    public function removeAvi(\UserBundle\Entity\Avis $avi)
    {
        $this->avis->removeElement($avi);
    }


    /**
     * Remove reservation
     *
     * @param \UserBundle\Entity\reservation $reservation
     */
    public function removeReservation(\UserBundle\Entity\reservation $reservation)
    {
        $this->reservations->removeElement($reservation);
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Annonce
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }


    /**
     * Add notification
     *
     * @param \UserBundle\Entity\Notification $notification
     *
     * @return Annonce
     */
    public function addNotification(\UserBundle\Entity\Notification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \UserBundle\Entity\Notification $notification
     */
    public function removeNotification(\UserBundle\Entity\Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }




    public function getType(){
        if(!is_null($this->getVtc()))
            return "Vtc";
        else if(!is_null($this->getTaxi()))
            return "Taxi";
        else if(!is_null($this->getDemenagement()))
            return "Demenagement";
        else if(!is_null($this->getCovoiturage()))
            return "Covoiturage";
        else
            return false;
    }


    public function getObject(){
        if(!is_null($this->getVtc()))
            return $this->getVtc();
        else if(!is_null($this->getTaxi()))
            return $this->getTaxi();
        else if(!is_null($this->getDemenagement()))
            return $this->getDemenagement();
        else if(!is_null($this->getCovoiturage()))
            return $this->getCovoiturage();
        else
            return false;
    }


    private function getMoyenneAvis(){
        if(count($this->getAvis()->toArray())>0){
            $totalAvis = count($this->getAvis()->toArray());
            $sumAvis = array_map(function($a){
                    return $a->getNote();
                }, $this->getAvis()->toArray());
            return ceil($sumAvis/$totalAvis);
        }else
            return null;

    }



    private function getEtat(){
        return true;
    }


    public function toArray($viewObject=false){
        $view =  array(
            'Id'=>$this->getId(),
            'Auteur'=>$this->getAuteur()->toArray(),

            // 'Reservations'=>$this->getReservations(),
            'Avis'=>$this->getMoyenneAvis(),
            'Rayon'=>$this->getRayon(),
            'Place'=>$this->getPlace(),
            'Etat'=>$this->getEtat(),
            'Terminer'=>$this->getTerminer(),
            'CreatedAt'=>$this->getCreatedAt()->getTimestamp(),
            'UpdatedAt'=>$this->getUpdatedAt()->getTimestamp()

        );

        if($viewObject){
            $view['Type'] = $this->getType() ? $this->getType() : false;
            // $view['Object'] = $this->getObject() ? $this->getObject()->toArray() : false;
            $view['Actif'] = $this->getActif();
        };

        return $view;



    }


    
}
