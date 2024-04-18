<?php

namespace AnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * covoiturage
 *
 * @ORM\Table(name="annonce_covoiturage")
 * @ORM\Entity(repositoryClass="AnnonceBundle\Repository\covoiturageRepository")
 */
class covoiturage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="AnnonceBundle\Entity\Annonce", mappedBy="covoiturage")
     */
    private $annonce;

    /**
     * @var bool
     *
     * @ORM\Column(name="regulier", type="boolean", nullable=true)
     */
    private $regulier;

    /**
     * @var text
     *
     * @ORM\Column(name="role", type="text", nullable=true)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="depart_id", referencedColumnName="id")
     */
    private $depart;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="arrivee_id", referencedColumnName="id")
     */
    private $arrivee;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="rendezvous_id", referencedColumnName="id")
     */
    private $rendezVous;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="depot_id", referencedColumnName="id")
     */
    private $depot;

    /**
     * @var array
     *
     * @ORM\Column(name="jours_aller", type="array", nullable=true)
     */
    private $jours_aller;

    /**
     * @var array
     *
     * @ORM\Column(name="jours_retour", type="array", nullable=true)
     */
    private $jours_retour;


    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="DateRegulier")
     * @ORM\JoinTable(name="annonce_covoiturage_dates",
     *      joinColumns={@ORM\JoinColumn(name="covoiturage_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="date_id", referencedColumnName="id")}
     *      )
     */
    private $dateReguliers;


    /**
     * @var array
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $flexibilite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_aller", type="time", nullable=true)
     */
    private $heureAller;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_retour", type="time", nullable=true)
     */
    private $heureRetour;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_unique", type="datetime", nullable=true)
     */
    private $dateUnique;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_unique", type="time", nullable=true)
     */
    private $heureUnique;

    /**
     * @var string
     *
     * @ORM\Column(type="array",nullable=true)
     */
    private $bagages;

    /**
     * @var string
     *
     * @ORM\Column(type="array",nullable=true)
     */
    private $preferences;


    /**
     * @var float
     *
     * @ORM\Column(name="cout", type="float")
     */
    private $cout;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlaces", type="smallint")
     */
    private $nbPlaces;

    /**
     * @var string
     *
     * @ORM\Column(name="infosdivers", type="text", nullable=true)
     */
    private $infosdivers;


    /**
     * @var string
     *
     * @ORM\Column(name="repondSous", type="text", nullable=true)
     */
    private $repondSous;

    /**
     * @var bool
     *
     * @ORM\Column(name="autoroute", type="boolean", nullable=true)
     */
    private $autoroute;


    /**
     * @var bool
     *
     * @ORM\Column(name="urgent", type="boolean")
     */
    private $urgent;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->urgent=false;
        $this->dateReguliers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->autoroute = false;
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
     * Set regulier
     *
     * @param boolean $regulier
     *
     * @return covoiturage
     */
    public function setRegulier($regulier)
    {
        $this->regulier = $regulier;

        return $this;
    }

    /**
     * Get regulier
     *
     * @return bool
     */
    public function getRegulier()
    {
        return $this->regulier;
    }

    /**
     * Set role
     *
     * @param array $role
     *
     * @return covoiturage
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return array
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set depart
     *
     * @param string $depart
     *
     * @return covoiturage
     */
    public function setDepart($depart)
    {
        $this->depart = $depart;

        return $this;
    }

    /**
     * Get depart
     *
     * @return string
     */
    public function getDepart()
    {
        return $this->depart;
    }

    /**
     * Set arrivee
     *
     * @param string $arrivee
     *
     * @return covoiturage
     */
    public function setArrivee($arrivee)
    {
        $this->arrivee = $arrivee;

        return $this;
    }

    /**
     * Get arrivee
     *
     * @return string
     */
    public function getArrivee()
    {
        return $this->arrivee;
    }

    /**
     * Set rendezVous
     *
     * @param string $rendezVous
     *
     * @return covoiturage
     */
    public function setRendezVous($rendezVous)
    {
        $this->rendezVous = $rendezVous;

        return $this;
    }

    /**
     * Get rendezVous
     *
     * @return string
     */
    public function getRendezVous()
    {
        return $this->rendezVous;
    }

    /**
     * Set depot
     *
     * @param string $depot
     *
     * @return covoiturage
     */
    public function setDepot($depot)
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * Get depot
     *
     * @return string
     */
    public function getDepot()
    {
        return $this->depot;
    }

    /**
     * Set jours
     *
     * @param array $jours
     *
     * @return covoiturage
     */
    public function setJours($jours)
    {
        $this->jours = $jours;

        return $this;
    }

    /**
     * Get jours
     *
     * @return array
     */
    public function getJours()
    {
        return $this->jours;
    }


    /**
     * Set joursAller
     *
     * @param array $joursAller
     *
     * @return covoiturage
     */
    public function setJoursAller($joursAller)
    {
        $this->jours_aller = $joursAller;

        return $this;
    }

    /**
     * Get joursAller
     *
     * @return array
     */
    public function getJoursAller()
    {
        if($this->jours_aller)
            sort($this->jours_aller, SORT_NUMERIC);
        return $this->jours_aller;
    }

    /**
     * Set joursRetour
     *
     * @param array $joursRetour
     *
     * @return covoiturage
     */
    public function setJoursRetour($joursRetour)
    {
        $this->jours_retour = $joursRetour;

        return $this;
    }

    /**
     * Get joursRetour
     *
     * @return array
     */
    public function getJoursRetour()
    {
        return $this->jours_retour;
    }

    /**
     * Set flexibilite
     *
     * @param string $flexibilite
     *
     * @return covoiturage
     */
    public function setFlexibilite($flexibilite)
    {
        $this->flexibilite = $flexibilite;

        return $this;
    }

    /**
     * Get flexibilite
     *
     * @return string
     */
    public function getFlexibilite()
    {
        return $this->flexibilite;
    }

    /**
     * Set heureAller
     *
     * @param \DateTime $heureAller
     *
     * @return covoiturage
     */
    public function setHeureAller($heureAller)
    {
        $this->heureAller = $heureAller;

        return $this;
    }

    /**
     * Get heureAller
     *
     * @return \DateTime
     */
    public function getHeureAller()
    {
        return $this->heureAller;
    }

    /**
     * Set heureRetour
     *
     * @param \DateTime $heureRetour
     *
     * @return covoiturage
     */
    public function setHeureRetour($heureRetour)
    {
        $this->heureRetour = $heureRetour;

        return $this;
    }

    /**
     * Get heureRetour
     *
     * @return \DateTime
     */
    public function getHeureRetour()
    {
        return $this->heureRetour;
    }

    /**
     * Set dateUnique
     *
     * @param \DateTime $dateUnique
     *
     * @return covoiturage
     */
    public function setDateUnique($dateUnique)
    {
        $this->dateUnique = $dateUnique;

        return $this;
    }

    /**
     * Get dateUnique
     *
     * @return \DateTime
     */
    public function getDateUnique()
    {
        return $this->dateUnique;
    }

    /**
     * Set heureUnique
     *
     * @param \DateTime $heureUnique
     *
     * @return covoiturage
     */
    public function setHeureUnique($heureUnique)
    {
        $this->heureUnique = $heureUnique;

        return $this;
    }

    /**
     * Get heureUnique
     *
     * @return \DateTime
     */
    public function getHeureUnique()
    {
        return $this->heureUnique;
    }

    /**
     * Set bagages
     *
     * @param array $bagages
     *
     * @return covoiturage
     */
    public function setBagages($bagages)
    {
        $this->bagages = $bagages;

        return $this;
    }

    /**
     * Get bagages
     *
     * @return array
     */
    public function getBagages()
    {
        return $this->bagages;
    }

    /**
     * Set preferences
     *
     * @param array $preferences
     *
     * @return covoiturage
     */
    public function setPreferences($preferences)
    {
        $this->preferences = $preferences;

        return $this;
    }

    /**
     * Get preferences
     *
     * @return array
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * Set cout
     *
     * @param float $cout
     *
     * @return covoiturage
     */
    public function setCout($cout)
    {
        $this->cout = $cout;

        return $this;
    }

    /**
     * Get cout
     *
     * @return float
     */
    public function getCout()
    {
        return $this->cout;
    }

    /**
     * Set nbPlaces
     *
     * @param integer $nbPlaces
     *
     * @return covoiturage
     */
    public function setNbPlaces($nbPlaces)
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    /**
     * Get nbPlaces
     *
     * @return int
     */
    public function getNbPlaces()
    {
        return $this->nbPlaces;
    }

    /**
     * Set infosdivers
     *
     * @param string $infosdivers
     *
     * @return covoiturage
     */
    public function setInfosdivers($infosdivers)
    {
        $this->infosdivers = $infosdivers;

        return $this;
    }

    /**
     * Get infosdivers
     *
     * @return string
     */
    public function getInfosdivers()
    {
        return $this->infosdivers;
    }

    

    /**
     * Set annonce
     *
     * @param \AnnonceBundle\Entity\Annonce $annonce
     *
     * @return covoiturage
     */
    public function setAnnonce(\AnnonceBundle\Entity\Annonce $annonce = null)
    {
        $this->annonce = $annonce;

        return $this;
    }

    /**
     * Get annonce
     *
     * @return \AnnonceBundle\Entity\Annonce
     */
    public function getAnnonce()
    {
        return $this->annonce;
    }


    public function getPlacesRestantes(){
        $reserver = array_sum(
            array_map(
                function($r){return $r->getNbPlaces();}, 
                $this->getAnnonce()->getReservations()->toArray()
            ));
        return $this->getNbPlaces() - $reserver;
    }





    /**
     * Add dateRegulier
     *
     * @param \AnnonceBundle\Entity\DateRegulier $dateRegulier
     *
     * @return covoiturage
     */
    public function addDateRegulier(\AnnonceBundle\Entity\DateRegulier $dateRegulier)
    {
        $this->dateReguliers[] = $dateRegulier;

        return $this;
    }

    /**
     * Remove dateRegulier
     *
     * @param \AnnonceBundle\Entity\DateRegulier $dateRegulier
     */
    public function removeDateRegulier(\AnnonceBundle\Entity\DateRegulier $dateRegulier)
    {
        $this->dateReguliers->removeElement($dateRegulier);
    }

    /**
     * Get dateReguliers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDateReguliers()
    {
        return $this->dateReguliers;
    }


    private static function cmpDate($a, $b){
        return $a->getDate() > $b->getDate();
    }


    public function genDateRegulier(){
        $dateValue = new \DateTime();

        $dates = array_filter($this->getDateReguliers()->toArray(), function($d) use ($dateValue){ return $d->getDate() >= $dateValue;});

        usort($dates, array($this,'cmpDate'));

        return $dates;
    }

    


    public function genNameDate(){
        $days = array("Lun","Mar","Mer","Jeu","Ven","Sam","Dim");
        return array_map(function($n) use ($days) {return $days[$n];}, $this->getJoursAller());
    }


    /**
     * Set urgent
     *
     * @param boolean $urgent
     *
     * @return covoiturage
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * Get urgent
     *
     * @return boolean
     */
    public function getUrgent()
    {
        return $this->urgent;
    }




    public function toArray(){

        if(!is_null($this->getJoursAller())){
            $days = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
            $joursAller = array_map(function($n) use ($days){return $days[$n];}, $this->getJoursAller());
        }else{
            $joursAller = null;
        }

        if(!is_null($this->getJoursRetour())){
            $days = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
            $joursRetour = array_map(function($n) use ($days){return $days[$n];}, $this->getJoursRetour());
        }else{
            $joursRetour = null;
        }


        return array(
            "Id"=>$this->getId(),
            "Annonce"=>$this->getAnnonce()->toArray(),
            "Regulier"=>$this->getRegulier(),
            "Role"=>$this->getRole(),
            "Depart"=>$this->getDepart()->toArray(),
            "Arrivee"=>$this->getArrivee()->toArray(),
            "RendezVous"=>$this->getRendezVous()->toArray(),
            "Depot"=>$this->getDepot()->toArray(),

            "JoursAller"=>$joursAller,
            "JoursRetour"=>$joursRetour,
            // "DateReguliers"=>is_null($this->getDateReguliers()) ? array() : array_map(function($d){return $d->getDate()->getTimestamp();}, $this->getDateReguliers()),
            "Flexibilite"=>$this->getFlexibilite(),
            "HeureAller"=>is_null($this->getHeureAller()) ? false : $this->getHeureAller()->getTimestamp(),
            "HeureRetour"=>is_null($this->getHeureRetour()) ? false : $this->getHeureRetour()->getTimestamp(),
            "DateUnique"=>is_null($this->getDateUnique()) ? false : $this->getDateUnique()->getTimestamp(),
            
            "Bagages"=>$this->getBagages(),
            "Preferences"=>$this->getPreferences(),
            "Cout"=>$this->getCout(),
            "NbPlaces"=>$this->getNbPlaces(),
            "Urgent"=>$this->getUrgent(),
            "InfosDivers"=>$this->getInfosDivers(),
            'PlacesRestantes'=>$this->getPlacesRestantes()
        );
    }
    

    

    

    

    /**
     * Set repondSous
     *
     * @param integer $repondSous
     *
     * @return covoiturage
     */
    public function setRepondSous($repondSous)
    {
        $this->repondSous = $repondSous;

        return $this;
    }

    /**
     * Get repondSous
     *
     * @return integer
     */
    public function getRepondSous()
    {
        return $this->repondSous;
    }

    /**
     * Set autoroute
     *
     * @param boolean $autoroute
     *
     * @return covoiturage
     */
    public function setAutoroute($autoroute)
    {
        $this->autoroute = $autoroute;

        return $this;
    }

    /**
     * Get autoroute
     *
     * @return boolean
     */
    public function getAutoroute()
    {
        return $this->autoroute;
    }
}
