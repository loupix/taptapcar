<?php

namespace AnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;


/**
 * vtc
 *
 * @ORM\Table(name="annonce_vtc")
 * @ORM\Entity(repositoryClass="AnnonceBundle\Repository\vtcRepository")
 */
class vtc
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
     * @ORM\OneToOne(targetEntity="AnnonceBundle\Entity\Annonce", mappedBy="vtc",cascade={"remove", "persist"})
     */
    private $annonce;


    /**
     * @var string
     *
     * @ORM\Column(name="Nom_societe", type="string", length=255)
     */
    private $nom_societe;

    /**
     * @var string
     *
     * @ORM\Column(name="Telephone_societe", type="string", length=255, nullable=true)
     * @AssertPhoneNumber(defaultRegion="FR")
     */
    private $telephone_societe;

    


    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Ville",cascade={"persist"})
     * @ORM\JoinColumn(name="ville_id", referencedColumnName="ville_id")
     */
    private $ville;


    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="DateRegulier")
     * @ORM\JoinTable(name="annonce_vtc_dates",
     *      joinColumns={@ORM\JoinColumn(name="vtc_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="date_id", referencedColumnName="id")}
     *      )
     */
    private $dateReguliers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_debut", type="time")
     */
    private $heureDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fin", type="time")
     */
    private $heureFin;

    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=255, nullable=true)
     */
    private $couleur;

    /**
     * @var bool
     *
     * @ORM\Column(name="wifi", type="boolean", nullable=true)
     */
    private $wifi;

    /**
     * @var array
     *
     * @ORM\Column(name="paiements", type="simple_array", nullable=true)
     */
    private $paiements;

    /**
     * @var bool
     *
     * @ORM\Column(name="facture", type="boolean", nullable=true)
     */
    private $facture;

    /**
     * @var bool
     *
     * @ORM\Column(name="siegeBebe", type="boolean", nullable=true)
     */
    private $siegeBebe;

    /**
     * @var bool
     *
     * @ORM\Column(name="animaux", type="boolean", nullable=true)
     */
    private $animaux;

    /**
     * @var float
     *
     * @ORM\Column(name="tarifJour", type="float", nullable=true)
     */
    private $tarifJour;


    /**
     * @var float
     *
     * @ORM\Column(name="tarifNuit", type="float", nullable=true)
     */
    private $tarifNuit;


    /**
     * @var string
     *
     * @ORM\Column(name="typeTarif", type="string")
     */
    private $typeTarif;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlaces", type="smallint")
     */
    private $nbPlaces;

    /**
     * @var bool
     *
     * @ORM\Column(name="urgent", type="boolean", nullable=true)
     */
    private $urgent;

    /**
     * @var array
     *
     * @ORM\Column(name="forfaits", type="simple_array", nullable=true)
     */
    private $forfaits;

    /**
     * @var string
     *
     * @ORM\Column(name="infosdivers", type="text",nullable=true)
     */
    private $infosdivers;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->urgent = false;
        $this->dateReguliers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ville
     *
     * @param string $ville
     *
     * @return vtc
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set heureDebut
     *
     * @param \DateTime $heureDebut
     *
     * @return vtc
     */
    public function setHeureDebut($heureDebut)
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    /**
     * Get heureDebut
     *
     * @return \DateTime
     */
    public function getHeureDebut()
    {
        return $this->heureDebut;
    }

    /**
     * Set heureFin
     *
     * @param \DateTime $heureFin
     *
     * @return vtc
     */
    public function setHeureFin($heureFin)
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    /**
     * Get heureFin
     *
     * @return \DateTime
     */
    public function getHeureFin()
    {
        return $this->heureFin;
    }

    /**
     * Set couleur
     *
     * @param string $couleur
     *
     * @return vtc
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set wifi
     *
     * @param boolean $wifi
     *
     * @return vtc
     */
    public function setWifi($wifi)
    {
        $this->wifi = $wifi;

        return $this;
    }

    /**
     * Get wifi
     *
     * @return bool
     */
    public function getWifi()
    {
        return $this->wifi;
    }

    /**
     * Set paiements
     *
     * @param array $paiements
     *
     * @return vtc
     */
    public function setPaiements($paiements)
    {
        $this->paiements = $paiements;

        return $this;
    }

    /**
     * Get paiements
     *
     * @return array
     */
    public function getPaiements()
    {
        return $this->paiements;
    }

    /**
     * Set facture
     *
     * @param boolean $facture
     *
     * @return vtc
     */
    public function setFacture($facture)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return bool
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set nbPlaces
     *
     * @param integer $nbPlaces
     *
     * @return vtc
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
     * Set forfaits
     *
     * @param array $forfaits
     *
     * @return vtc
     */
    public function setForfaits($forfaits)
    {
        $this->forfaits = $forfaits;

        return $this;
    }

    /**
     * Get forfaits
     *
     * @return array
     */
    public function getForfaits()
    {
        return $this->forfaits;
    }

    /**
     * Set infosdivers
     *
     * @param string $infosdivers
     *
     * @return vtc
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
     * @return vtc
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
     * @return vtc
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


    /**
     * Set urgent
     *
     * @param boolean $urgent
     *
     * @return vtc
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
    

    /**
     * Set siegeBebe
     *
     * @param boolean $siegeBebe
     *
     * @return vtc
     */
    public function setSiegeBebe($siegeBebe)
    {
        $this->siegeBebe = $siegeBebe;

        return $this;
    }

    /**
     * Get siegeBebe
     *
     * @return boolean
     */
    public function getSiegeBebe()
    {
        return $this->siegeBebe;
    }

    /**
     * Set animaux
     *
     * @param boolean $animaux
     *
     * @return vtc
     */
    public function setAnimaux($animaux)
    {
        $this->animaux = $animaux;

        return $this;
    }

    /**
     * Get animaux
     *
     * @return boolean
     */
    public function getAnimaux()
    {
        return $this->animaux;
    }


    /**
     * Set nomSociete
     *
     * @param string $nomSociete
     *
     * @return vtc
     */
    public function setNomSociete($nomSociete)
    {
        $this->nom_societe = $nomSociete;

        return $this;
    }

    /**
     * Get nomSociete
     *
     * @return string
     */
    public function getNomSociete()
    {
        return $this->nom_societe;
    }

    /**
     * Set telephoneSociete
     *
     * @param string $telephoneSociete
     *
     * @return vtc
     */
    public function setTelephoneSociete($telephoneSociete)
    {
        $this->telephone_societe = $telephoneSociete;

        return $this;
    }

    /**
     * Get telephoneSociete
     *
     * @return string
     */
    public function getTelephoneSociete()
    {
        return $this->telephone_societe;
    }


    public function genDate($n){
        $date = new \DateTime();
        if($n>0)
            $date->add(new \DateInterval('P'.$n.'D'));
        return $date;
    }


    /**
     * Set tarifJour
     *
     * @param float $tarifJour
     *
     * @return vtc
     */
    public function setTarifJour($tarifJour)
    {
        $this->tarifJour = $tarifJour;

        return $this;
    }

    /**
     * Get tarifJour
     *
     * @return float
     */
    public function getTarifJour()
    {
        return number_format($this->tarifJour, 2);
    }

    /**
     * Set tarifNuit
     *
     * @param float $tarifNuit
     *
     * @return vtc
     */
    public function setTarifNuit($tarifNuit)
    {
        $this->tarifNuit = $tarifNuit;

        return $this;
    }

    /**
     * Get tarifNuit
     *
     * @return float
     */
    public function getTarifNuit()
    {
        return number_format($this->tarifNuit, 2);
    }

    /**
     * Set typeTarif
     *
     * @param string $typeTarif
     *
     * @return vtc
     */
    public function setTypeTarif($typeTarif)
    {
        $this->typeTarif = $typeTarif;

        return $this;
    }

    /**
     * Get typeTarif
     *
     * @return string
     */
    public function getTypeTarif()
    {
        return $this->typeTarif;
    }

    public function getIsDay(){
        // Lat & lon de paris
        $lat = 48.8534100;
        $lon = 2.3488000;

        $sunset = date_sunset(time(), SUNFUNCS_RET_DOUBLE, $lat, $lon, 96, 2);
        $sunrise = date_sunrise(time(), SUNFUNCS_RET_DOUBLE, $lat, $lon, 96, 2);
        $now = date("H") + date("i") / 60 + date("s") / 3600;
        if($sunrise < $sunset)
            if (($now > $sunrise) && ($now < $sunset)) return true;
            else return false;
        else
            if (($now > $sunrise) || ($now < $sunset)) return true;
            else return false;

    }

    public function getCout(){
        if($this->getIsDay())
            return $this->getTarifJour();
        else
            return $this->getTarifNuit();
    }


    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'HeureDebut'=>$this->getHeureDebut()->getTimestamp(),
            'HeureFin'=>$this->getHeureFin()->getTimestamp(),
            'Ville'=>$this->getVille()->toArray(),

            'Couleur'=>$this->getCouleur(),
            'Wifi'=>$this->getWifi(),
            'Animaux'=>$this->getAnimaux(),
            'SiegeBebe'=>$this->getSiegeBebe(),
            'Facture'=>$this->getFacture(),

            'TypeTarif'=>$this->getTypeTarif(),
            'IsDay'=>$this->getIsDay(),
            'TarifNuit'=>$this->getTarifNuit(),
            'TarifJour'=>$this->getTarifJour(),
            'NbPlaces'=>$this->getNbPlaces(),
            'Paiements'=>$this->getPaiements(),
            // 'Forfaits'=>$this->getForfaits(),
            'Urgent'=>$this->getUrgent(),
            'Infosdivers'=>$this->getInfosdivers(),
            'Annonce'=>$this->getAnnonce()->toArray(),
            "PlacesRestantes"=>$this->getNbPlaces()

        );
    }



    
}
