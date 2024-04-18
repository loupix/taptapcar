<?php

namespace AnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;


/**
 * Demenagement
 *
 * @ORM\Table(name="annonce_demenagement")
 * @ORM\Entity(repositoryClass="AnnonceBundle\Repository\DemenagementRepository")
 */
class Demenagement
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
     * @ORM\OneToOne(targetEntity="AnnonceBundle\Entity\Annonce", mappedBy="demenagement")
     */
    private $annonce;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom_societe", type="string", length=255, nullable=true)
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
     * @var bool
     *
     * @ORM\Column(name="transporteur", type="boolean")
     */
    private $transporteur;


    /**
     * @var ville
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Ville",cascade={"persist"})
     * @ORM\JoinColumn(name="ville_id", referencedColumnName="ville_id")
     */
    private $ville;


    /**
     * @var lieu
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="lieu_id", referencedColumnName="id")
     */
    private $lieu;


    /**
     * @var depart
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="depart_id", referencedColumnName="id")
     */
    private $depart;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="rendezvous_id", referencedColumnName="id")
     */
    private $rendezVous;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="arrive_id", referencedColumnName="id")
     */
    private $arrivee;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="GeographieBundle\Entity\Place",cascade={"persist"})
     * @ORM\JoinColumn(name="depot_id", referencedColumnName="id")
     */
    private $depot;

    /**
     * @var int
     *
     * @ORM\Column(name="flexibilite", type="string", nullable=true)
     */
    private $flexibilite;

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
     * @ORM\ManyToMany(targetEntity="DateRegulier",cascade={"persist"})
     * @ORM\JoinTable(name="annonce_demenagement_dates",
     *      joinColumns={@ORM\JoinColumn(name="demenagement_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="date_id", referencedColumnName="id")}
     *      )
     */
    private $dateReguliers;

    /**
     * @var date
     *
     * @ORM\Column(name="jours_unique", type="date", nullable=true)
     */
    private $jours_unique;

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
     * @ORM\Column(name="utilitaire", type="string", length=255, nullable=true)
     */
    private $utilitaire;

    /**
     * @var string
     *
     * @ORM\Column(name="volume", type="string", length=255, nullable=true)
     */
    private $volume;

    /**
     * @var string
     *
     * @ORM\Column(name="hauteur", type="string", length=255, nullable=true)
     */
    private $hauteur;

    /**
     * @var string
     *
     * @ORM\Column(name="largeur", type="string", length=255, nullable=true)
     */
    private $largeur;

    /**
     * @var string
     *
     * @ORM\Column(name="longueur", type="string", length=255, nullable=true)
     */
    private $longueur;

    /**
     * @var float
     *
     * @ORM\Column(name="tarif", type="float", nullable=true)
     */
    private $tarif;


    /**
     * @var string
     *
     * @ORM\Column(name="budgetApproximatif", type="string", nullable=true)
     */
    private $budgetApproximatif;

    /**
     * @var string
     *
     * @ORM\Column(name="paiements", type="simple_array", nullable=true)
     */
    private $paiements;

    /**
     * @var string
     *
     * @ORM\Column(name="infosdivers", type="text",nullable=true)
     */
    private $infosdivers;

    /**
     * @var bool
     *
     * @ORM\Column(name="urgent", type="boolean")
     */
    private $urgent;


    /**
     * @var string
     *
     * @ORM\Column(name="tarification", type="string",nullable=true)
     */
    private $tarification;


    /**
     * @var devis
     *
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Devis",cascade={"remove","persist"})
     * @ORM\JoinTable(name="annonce_demenagement_devis",
     *      joinColumns={@ORM\JoinColumn(name="demenagement_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="devis_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $devis;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->urgent = false;
        $this->dateReguliers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->devis = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \GeographieBundle\Entity\Ville $ville
     *
     * @return Demenagement
     */
    public function setVille(\GeographieBundle\Entity\Ville $ville = null)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return \GeographieBundle\Entity\Ville
     */
    public function getVille()
    {
        return $this->ville;
    }
    

    /**
     * Set depart
     *
     * @param integer $depart
     *
     * @return Demenagement
     */
    public function setDepart($depart=null)
    {
        $this->depart = $depart;

        return $this;
    }

    /**
     * Get depart
     *
     * @return int
     */
    public function getDepart()
    {
        return $this->depart;
    }

    /**
     * Set rendezVous
     *
     * @param string $rendezVous
     *
     * @return Demenagement
     */
    public function setRendezVous($rendezVous=null)
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
     * Set arrivee
     *
     * @param integer $arrivee
     *
     * @return Demenagement
     */
    public function setArrivee($arrivee=null)
    {
        $this->arrivee = $arrivee;

        return $this;
    }

    /**
     * Get arrivee
     *
     * @return int
     */
    public function getArrivee()
    {
        return $this->arrivee;
    }

    /**
     * Set depot
     *
     * @param integer $depot
     *
     * @return Demenagement
     */
    public function setDepot($depot=null)
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * Get depot
     *
     * @return int
     */
    public function getDepot()
    {
        return $this->depot;
    }

    /**
     * Set disponnibilite
     *
     * @param integer $disponnibilite
     *
     * @return Demenagement
     */
    public function setDisponnibilite($disponnibilite)
    {
        $this->disponnibilite = $disponnibilite;

        return $this;
    }

    /**
     * Get disponnibilite
     *
     * @return int
     */
    public function getDisponnibilite()
    {
        return $this->disponnibilite;
    }

    /**
     * Set flexibilite
     *
     * @param string $flexibilite
     *
     * @return Demenagement
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
     * Set horraires
     *
     * @param integer $horraires
     *
     * @return Demenagement
     */
    public function setHorraires($horraires)
    {
        $this->horraires = $horraires;

        return $this;
    }

    /**
     * Get horraires
     *
     * @return int
     */
    public function getHorraires()
    {
        return $this->horraires;
    }


    /**
     * Set joursAller
     *
     * @param array $joursAller
     *
     * @return Demenagement
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
     * @return Demenagement
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
     * Set utilitaire
     *
     * @param string $utilitaire
     *
     * @return Demenagement
     */
    public function setUtilitaire($utilitaire)
    {
        $this->utilitaire = $utilitaire;

        return $this;
    }

    /**
     * Get utilitaire
     *
     * @return string
     */
    public function getUtilitaire()
    {
        return $this->utilitaire;
    }

    /**
     * Set volume
     *
     * @param string $volume
     *
     * @return Demenagement
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * Get volume
     *
     * @return string
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Set hauteur
     *
     * @param string $hauteur
     *
     * @return Demenagement
     */
    public function setHauteur($hauteur)
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    /**
     * Get hauteur
     *
     * @return string
     */
    public function getHauteur()
    {
        return $this->hauteur;
    }

    /**
     * Set longueur
     *
     * @param string $longueur
     *
     * @return Demenagement
     */
    public function setLongueur($longueur)
    {
        $this->longueur = $longueur;

        return $this;
    }

    /**
     * Get longueur
     *
     * @return string
     */
    public function getLongueur()
    {
        return $this->longueur;
    }

    /**
     * Set tarif
     *
     * @param float $tarif
     *
     * @return Demenagement
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return float
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set paiements
     *
     * @param string $paiements
     *
     * @return Demenagement
     */
    public function setPaiements($paiements)
    {
        $this->paiements = $paiements;

        return $this;
    }

    /**
     * Get paiements
     *
     * @return string
     */
    public function getPaiements()
    {
        return $this->paiements;
    }

    /**
     * Set infosdivers
     *
     * @param string $infosdivers
     *
     * @return Demenagement
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
     * Set heureAller
     *
     * @param \DateTime $heureAller
     *
     * @return Demenagement
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
     * @return Demenagement
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
     * Set heureDebut
     *
     * @param \DateTime $heureDebut
     *
     * @return Demenagement
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
     * @return Demenagement
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
     * Set joursUnique
     *
     * @param \DateTime $joursUnique
     *
     * @return Demenagement
     */
    public function setJoursUnique($joursUnique)
    {
        $this->jours_unique = $joursUnique;

        return $this;
    }

    /**
     * Get joursUnique
     *
     * @return \DateTime
     */
    public function getJoursUnique()
    {
        return $this->jours_unique;
    }

    /**
     * Set heureUnique
     *
     * @param \DateTime $heureUnique
     *
     * @return Demenagement
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
     * Set budgetApproximatif
     *
     * @param string $budgetApproximatif
     *
     * @return Demenagement
     */
    public function setBudgetApproximatif($budgetApproximatif)
    {
        $this->budgetApproximatif = $budgetApproximatif;

        return $this;
    }

    /**
     * Get budgetApproximatif
     *
     * @return string
     */
    public function getBudgetApproximatif()
    {
        return $this->budgetApproximatif;
    }

    /**
     * Set annonce
     *
     * @param \AnnonceBundle\Entity\Annonce $annonce
     *
     * @return Demenagement
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



    /**
     * Set dateUnique
     *
     * @param \DateTime $dateUnique
     *
     * @return Demenagement
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
     * @return Demenagement
     */
    public function addDateRegulier(\AnnonceBundle\Entity\DateRegulier $dateRegulier)
    {   
        if(!$this->dateReguliers->contains($dateRegulier))
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

    /**
     * Set urgent
     *
     * @param boolean $urgent
     *
     * @return Demenagement
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
     * Set tarification
     *
     * @param string $tarification
     *
     * @return Demenagement
     */
    public function setTarification($tarification)
    {
        $this->tarification = $tarification;

        return $this;
    }

    /**
     * Get tarification
     *
     * @return string
     */
    public function getTarification()
    {
        return $this->tarification;
    }


    /**
     * Set nomSociete
     *
     * @param string $nomSociete
     *
     * @return Demenagement
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
     * @return Demenagement
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


    /**
     * Add devi
     *
     * @param \UserBundle\Entity\Devis $devis
     *
     * @return Demenagement
     */
    public function addDevis(\UserBundle\Entity\Devis $devi)
    {
        $this->devis[] = $devi;

        return $this;
    }

    /**
     * Remove devis
     *
     * @param \UserBundle\Entity\Devis $devis
     */
    public function removeDevis(\UserBundle\Entity\Devis $devi)
    {
        $this->devis->removeElement($devi);
    }

    /**
     * Get devis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDevis()
    {
        return $this->devis;
    }

    /**
     * Add devi
     *
     * @param \UserBundle\Entity\Devis $devi
     *
     * @return Demenagement
     */
    public function addDevi(\UserBundle\Entity\Devis $devi)
    {
        $this->devis[] = $devi;

        return $this;
    }

    /**
     * Remove devi
     *
     * @param \UserBundle\Entity\Devis $devi
     */
    public function removeDevi(\UserBundle\Entity\Devis $devi)
    {
        $this->devis->removeElement($devi);
    }


    public function genNameDate(){
        $days = array("Lun","Mar","Mer","Jeu","Ven","Sam","Dim");

        return array_map(function($n) use ($days) {return $days[$n];}, $this->getJoursAller());
    }



    /**
     * Set transporteur
     *
     * @param boolean $transporteur
     *
     * @return Demenagement
     */
    public function setTransporteur($transporteur)
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    /**
     * Get transporteur
     *
     * @return boolean
     */
    public function getTransporteur()
    {
        return $this->transporteur;
    }


    /**
     * Set lieu
     *
     * @param \GeographieBundle\Entity\Place $lieu
     *
     * @return Demenagement
     */
    public function setLieu($lieu = null)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu
     *
     * @return \GeographieBundle\Entity\Place
     */
    public function getLieu()
    {
        return $this->lieu;
    }


    /**
     * Set largeur
     *
     * @param string $largeur
     *
     * @return Demenagement
     */
    public function setLargeur($largeur)
    {
        $this->largeur = $largeur;

        return $this;
    }

    /**
     * Get largeur
     *
     * @return string
     */
    public function getLargeur()
    {
        return $this->largeur;
    }




    public function toArray(){
        $arr = array(
            "Id"=>$this->getId(),

            "Annonce"=>$this->getAnnonce()->toArray(),
            
            "Flexibilite"=>$this->getFlexibilite(),

            "Transporteur"=>$this->getTransporteur(),

            "Urgent"=>$this->getUrgent(),
            "InfosDivers"=>$this->getInfosDivers()
        );

        if($this->getTransporteur()){
            $arr['NomSociete'] = $this->getNomSociete();
            $arr['JoursAller'] = $this->getJoursAller();
            $arr['NameAller'] = join(", ",$this->genNameDate());
            $arr['JoursRetour'] = $this->getJoursRetour();
            $arr['HeureAller'] = $this->getHeureAller()->getTimestamp();
            $arr['HeureRetour'] = $this->getHeureRetour()->getTimestamp();
            $arr['Utilitaire'] = $this->getUtilitaire();
            $arr['Volume'] = $this->getVolume();
            $arr['Hauteur'] = $this->getHauteur();
            $arr['Largeur'] = $this->getLargeur();
            $arr['Longueur'] = $this->getLongueur();
            $arr['Tarif'] = $this->getTarif();
            $arr['Tarification'] = $this->getTarification();
            $arr['Paiements'] = $this->getPaiements();
        }else{
            $arr['BudgetApproximatif'] = $this->getBudgetApproximatif();
            $arr['JoursUnique'] = $this->getJoursUnique()->getTimestamp();
            $arr['HeureUnique'] = $this->getHeureUnique()->getTimestamp();
        };


        if(!is_null($this->getDepart())){
            $arr['Depart'] = $this->getDepart()->toArray();
            $arr['Arrivee'] = $this->getArrivee()->toArray();
            $arr['RendezVous'] = $this->getRendezVous()->toArray();
            $arr['Depot'] = $this->getDepot()->toArray();
        }else if(!is_null($this->getVille())){
            $arr['Lieu'] = $this->getLieu()->toArray();
        }
        return $arr;
    }


}
