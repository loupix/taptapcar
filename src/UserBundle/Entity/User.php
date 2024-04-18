<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\UserBundle\Entity\User as BaseUser;
use AnnnoceBundle\Entity\Annonce;
use UserBundle\Entity\Profile;
use UserBundle\Entity\Photo;
use UserBundle\Entity\System;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser implements ParticipantInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="anneeNaissance", type="integer")
     */
    private $anneeNaissance;


    /**
     * @var profile
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Profile", mappedBy="user", cascade={"remove", "persist"})
     */
    private $profile;

    /**
     * @var annonces
     *
     * @ORM\OneToMany(targetEntity="AnnonceBundle\Entity\Annonce", mappedBy="auteur", cascade={"remove", "persist"})
     */
    private $annonces;

    /**
     * @var notifications
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Notification", mappedBy="user", cascade={"remove", "persist"})
     */
    private $notifications;

    /**
     * @var reservations
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Reservation", mappedBy="auteur", cascade={"remove", "persist"})
     *
     */
    private $reservations;


    /**
     * @var devis
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Devis", mappedBy="auteur", cascade={"remove", "persist"})
     *
     */
    private $auteurDevis;


    /**
     * @var devis
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Devis", mappedBy="client", cascade={"remove", "persist"})
     *
     */
    private $clientDevis;


    /**
     * @var avis
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Avis", mappedBy="auteur", cascade={"remove", "persist"})
     *
     */
    private $auteurAvis;


    /**
     * @var avis
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Avis", mappedBy="client", cascade={"remove", "persist"})
     *
     */
    private $clientAvis;



    /**
     * @var Paiement
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Paiement", mappedBy="auteur", cascade={"remove", "persist"})
     *
     */
    private $auteurPaiement;


    /**
     * @var Paiement
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\Paiement", mappedBy="transporteur", cascade={"remove", "persist"})
     *
     */
    private $transporteurPaiement;



    /**
     * @var boolean
     *
     * @ORM\Column(name="isFacebook", type="boolean")
     */
    private $isFacebook;


    /**
     * @var integer
     *
     * @ORM\Column(name="facebook_id", type="string", nullable=true)
     */
    protected $facebook_id;



    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isGoogle", type="boolean", nullable=true)
     */
    private $isGoogle;


    /**
     * @var integer
     *
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    protected $google_id;


    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;


    /**
     * @var boolean
     *
     * @ORM\Column(name="verifie", type="boolean")
     */
    private $verifie;


    /**
     * @var boolean
     *
     * @ORM\Column(name="conditionSite", type="boolean")
     */
    private $conditionSite;

    /**
     * @var system
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\System", mappedBy="user", cascade={"remove", "persist"})
     */
    private $system;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new \DateTime();
        $this->verifie = false;
        $this->isFacebook = false;
        $this->isGoogle = false;

        $system = new System();
        $profile = new Profile();

        // $photo = new Photo();
        // $photo->setSrc("profil.jpg");
        // $photo->setWidth(120);
        // $photo->setHeight(120);
        // $photo->setProfile($profile);

        // $profile->setPhoto($photo);
        $profile->setUser($this);
        $system->setUser($this);
        
        $this->setProfile($profile);
        $this->setSystem($system);
    }



    public function getParent()
    {
        return 'FOSUserBundle';
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
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set anneeNaissance
     *
     * @param \DateTime $anneeNaissance
     *
     * @return User
     */
    public function setAnneeNaissance($anneeNaissance)
    {
        $this->anneeNaissance = $anneeNaissance;

        return $this;
    }

    /**
     * Get anneeNaissance
     *
     * @return \DateTime
     */
    public function getAnneeNaissance()
    {
        return $this->anneeNaissance;
    }



        /**
     * Set annonces
     *
     * @param integer $annonces
     *
     * @return Profile
     */
    public function setAnnonces($annonces)
    {
        $this->annonces = $annonces;

        return $this;
    }

    /**
     * Get annonces
     *
     * @return int
     */
    public function getAnnonces()
    {
        return $this->annonces;
    }

    /**
     * Set reservations
     *
     * @param string $reservations
     *
     * @return User
     */
    public function setReservations($reservations)
    {
        $this->reservations = $reservations;

        return $this;
    }

    /**
     * Get reservations
     *
     * @return string
     */
    public function getReservations()
    {
        return $this->reservations;
    }


    /**
     * Set profile
     *
     * @param integer $profile
     *
     * @return User
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return integer
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Add annonce
     *
     * @param \AnnonceBundle\Entity\Annonce $annonce
     *
     * @return User
     */
    public function addAnnonce(\AnnonceBundle\Entity\Annonce $annonce)
    {
        $this->annonces[] = $annonce;

        return $this;
    }

    /**
     * Remove annonce
     *
     * @param \AnnonceBundle\Entity\Annonce $annonce
     */
    public function removeAnnonce(\AnnonceBundle\Entity\Annonce $annonce)
    {
        $this->annonces->removeElement($annonce);
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
     * Set passwordConfirmation
     *
     * @param string $passwordConfirmation
     *
     * @return User
     */
    public function setPasswordConfirmation($passwordConfirmation)
    {
        $this->passwordConfirmation = $passwordConfirmation;

        return $this;
    }

    /**
     * Get passwordConfirmation
     *
     * @return string
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }

    /**
     * Add reservation
     *
     * @param \AnnonceBundle\Entity\Annonce $reservation
     *
     * @return User
     */
    public function addReservation(\AnnonceBundle\Entity\Annonce $reservation)
    {
        $this->reservations[] = $reservation;

        return $this;
    }

    /**
     * Remove reservation
     *
     * @param \AnnonceBundle\Entity\Annonce $reservation
     */
    public function removeReservation(\AnnonceBundle\Entity\Annonce $reservation)
    {
        $this->reservations->removeElement($reservation);
    }

    /**
     * Add auteurDevi
     *
     * @param \UserBundle\Entity\Devis $auteurDevi
     *
     * @return User
     */
    public function addAuteurDevi(\UserBundle\Entity\Devis $auteurDevi)
    {
        $this->auteurDevis[] = $auteurDevi;

        return $this;
    }

    /**
     * Remove auteurDevi
     *
     * @param \UserBundle\Entity\Devis $auteurDevi
     */
    public function removeAuteurDevi(\UserBundle\Entity\Devis $auteurDevi)
    {
        $this->auteurDevis->removeElement($auteurDevi);
    }

    /**
     * Get auteurDevis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuteurDevis()
    {
        return $this->auteurDevis;
    }

    /**
     * Add clientDevi
     *
     * @param \UserBundle\Entity\Devis $clientDevi
     *
     * @return User
     */
    public function addClientDevi(\UserBundle\Entity\Devis $clientDevi)
    {
        $this->clientDevis[] = $clientDevi;

        return $this;
    }

    /**
     * Remove clientDevi
     *
     * @param \UserBundle\Entity\Devis $clientDevi
     */
    public function removeClientDevi(\UserBundle\Entity\Devis $clientDevi)
    {
        $this->clientDevis->removeElement($clientDevi);
    }

    /**
     * Get clientDevis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientDevis()
    {
        return $this->clientDevis;
    }

    /**
     * Add auteurAvi
     *
     * @param \UserBundle\Entity\Avis $auteurAvi
     *
     * @return User
     */
    public function addAuteurAvi(\UserBundle\Entity\Avis $auteurAvi)
    {
        $this->auteurAvis[] = $auteurAvi;

        return $this;
    }

    /**
     * Remove auteurAvi
     *
     * @param \UserBundle\Entity\Avis $auteurAvi
     */
    public function removeAuteurAvi(\UserBundle\Entity\Avis $auteurAvi)
    {
        $this->auteurAvis->removeElement($auteurAvi);
    }

    /**
     * Get auteurAvis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuteurAvis()
    {
        return $this->auteurAvis;
    }

    /**
     * Add clientAvi
     *
     * @param \UserBundle\Entity\Avis $clientAvi
     *
     * @return User
     */
    public function addClientAvi(\UserBundle\Entity\Avis $clientAvi)
    {
        $this->clientAvis[] = $clientAvi;

        return $this;
    }

    /**
     * Remove clientAvi
     *
     * @param \UserBundle\Entity\Avis $clientAvi
     */
    public function removeClientAvi(\UserBundle\Entity\Avis $clientAvi)
    {
        $this->clientAvis->removeElement($clientAvi);
    }

    /**
     * Get clientAvis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientAvis()
    {
        return $this->clientAvis;
    }

    /**
     * Add notification
     *
     * @param \UserBundle\Entity\Notification $notification
     *
     * @return User
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

    /**
     * Add auteurPaiement
     *
     * @param \UserBundle\Entity\Paiement $auteurPaiement
     *
     * @return User
     */
    public function addAuteurPaiement(\UserBundle\Entity\Paiement $auteurPaiement)
    {
        $this->auteurPaiement[] = $auteurPaiement;

        return $this;
    }

    /**
     * Remove auteurPaiement
     *
     * @param \UserBundle\Entity\Paiement $auteurPaiement
     */
    public function removeAuteurPaiement(\UserBundle\Entity\Paiement $auteurPaiement)
    {
        $this->auteurPaiement->removeElement($auteurPaiement);
    }

    /**
     * Get auteurPaiement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuteurPaiement()
    {
        return $this->auteurPaiement;
    }

    /**
     * Add clientPaiement
     *
     * @param \UserBundle\Entity\Paiement $clientPaiement
     *
     * @return User
     */
    public function addClientPaiement(\UserBundle\Entity\Paiement $clientPaiement)
    {
        $this->clientPaiement[] = $clientPaiement;

        return $this;
    }

    /**
     * Remove clientPaiement
     *
     * @param \UserBundle\Entity\Paiement $clientPaiement
     */
    public function removeClientPaiement(\UserBundle\Entity\Paiement $clientPaiement)
    {
        $this->clientPaiement->removeElement($clientPaiement);
    }

    /**
     * Get clientPaiement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientPaiement()
    {
        return $this->clientPaiement;
    }


    public function getAge()
    {
        
        $dt1 =  new \DateTime(date($this->getAnneeNaissance()."-01-01 00:00:00"));

        $dateInterval = $dt1->diff(new \DateTime(date('Y-m-d H:i')));
 
        return $dateInterval->y;
    }

    /**
     * Add transporteurPaiement
     *
     * @param \UserBundle\Entity\Paiement $transporteurPaiement
     *
     * @return User
     */
    public function addTransporteurPaiement(\UserBundle\Entity\Paiement $transporteurPaiement)
    {
        $this->transporteurPaiement[] = $transporteurPaiement;

        return $this;
    }

    /**
     * Remove transporteurPaiement
     *
     * @param \UserBundle\Entity\Paiement $transporteurPaiement
     */
    public function removeTransporteurPaiement(\UserBundle\Entity\Paiement $transporteurPaiement)
    {
        $this->transporteurPaiement->removeElement($transporteurPaiement);
    }

    /**
     * Get transporteurPaiement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransporteurPaiement()
    {
        return $this->transporteurPaiement;
    }



    /**
     * Set verifie
     *
     * @param boolean $verifie
     *
     * @return User
     */
    public function setVerifie($verifie)
    {
        $this->verifie = $verifie;

        return $this;
    }

    /**
     * Get verifie
     *
     * @return boolean
     */
    public function getVerifie()
    {
        return $this->verifie;
    }





    /**
     * Set isFacebook
     *
     * @param boolean $isFacebook
     *
     * @return User
     */
    public function setIsFacebook($isFacebook)
    {
        $this->isFacebook = $isFacebook;

        return $this;
    }

    /**
     * Get isFacebook
     *
     * @return boolean
     */
    public function getIsFacebook()
    {
        return $this->isFacebook;
    }

    /**
     * Set facebookId
     *
     * @param integer $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->setIsFacebook(true);
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return integer
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }



    /**
     * Set isGoogle
     *
     * @param boolean $isGoogle
     *
     * @return User
     */
    public function setIsGoogle($isGoogle)
    {
        $this->isGoogle = $isGoogle;

        return $this;
    }

    /**
     * Get isGoogle
     *
     * @return boolean
     */
    public function getIsGoogle()
    {
        return $this->isGoogle;
    }

    /**
     * Set googleId
     *
     * @param integer $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->setIsGoogle(true);
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return integer
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }






    public function getNote(){
        $allNotes = array_map(function($a){return $a->getNote()-1;}, $this->getClientAvis()->toArray());
        if(count($allNotes)==0)
            return 0;
        return round(array_sum($allNotes)/count($allNotes));


    }


    public function serialize()
    {
        return serialize(array($this->facebook_id, parent::serialize()));
    }

    public function unserialize($data)
    {
        list($this->facebook_id, $parentData) = unserialize($data);
        parent::unserialize($parentData);
    }

    public function setFBData($fbdata)
    {
        if (isset($fbdata['id'])) {
            $this->setFacebookId($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
            $this->setIsFacebook(true);
        }
        if (isset($fbdata['first_name'])) {
            $this->setPrenom($fbdata['first_name']);
        }
        // if (isset($fbdata['name'])) {
        //     $this->setUsername($fbdata['name']);
        // }
        if (isset($fbdata['last_name'])) {
            $this->setNom($fbdata['last_name']);
        }
        if (isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
        if (isset($fbdata['birthday'])) {
            $this->setAnneeNaissance($fbdata['birthday']);
        }
    }


    /**
     * Set system
     *
     * @param \UserBundle\Entity\System $system
     *
     * @return User
     */
    public function setSystem(\UserBundle\Entity\System $system = null)
    {
        $this->system = $system;

        return $this;
    }

    /**
     * Get system
     *
     * @return \UserBundle\Entity\System
     */
    public function getSystem()
    {
        return $this->system;
    }


    public function getTelephone(){
        return null;
    }



    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set googleAccessToken
     *
     * @param string $googleAccessToken
     *
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get googleAccessToken
     *
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }   


    /**
     * Set conditionSite
     *
     * @param boolean $conditionSite
     *
     * @return User
     */
    public function setConditionSite($conditionSite)
    {
        $this->conditionSite = $conditionSite;

        return $this;
    }

    /**
     * Get conditionSite
     *
     * @return boolean
     */
    public function getConditionSite()
    {
        return $this->conditionSite;
    }



    public function toArray($role=false, $system=false){
        $arr = array(
            'Id'=>$this->getId(),
            'Nom'=>$this->getNom(),
            'Prenom'=>$this->getPrenom(),
            "Email"=>$this->getEmail(),
            "Age"=>$this->getAge(),
            "Note"=>$this->getNote(),
            "Verifie"=>$this->getVerifie(),
            "CreatedAt"=>$this->getCreatedAt()->getTimestamp(),
            "LastLogin"=>$this->getLastLogin()->getTimestamp(),
            // "token"=>$this->getToken(),
            'Profile'=>$this->getProfile()->toArray()
        );
        if($role){
            $arr['Roles'] = $this->getRoles();
            $arr['Admin'] = $this->hasRole("ROLE_ADMIN");
            // $arr['Ip'] = $this->getSystem()->getIp();
        }

        if($system){
            $arr['system'] = $this->getSystem()->toArray();
            $arr['Telephone'] = $this->getTelephone();
        }
        return $arr;
    }


    

    

    

    

    
}
