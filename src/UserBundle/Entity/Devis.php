<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Devis
 *
 * @ORM\Table(name="user_devis")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\DevisRepository")
 */
class Devis
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
     * @var user
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="auteurDevis")
     * @ORM\JoinColumn(name="auteur_id", referencedColumnName="id")
     */
    private $auteur;

    /**
     * @var user
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="clientDevis")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @var annonce
     *
     * @ORM\ManyToOne(targetEntity="AnnonceBundle\Entity\Annonce")
     * @ORM\JoinColumn(name="annonce_id", referencedColumnName="id", nullable=true)
     */
    private $annonce;

    /**
     * @var integer
     *
     * @ORM\Column(name="distance", type="integer")
     */
    private $distance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duree", type="time")
     */
    private $duree;

    /**
     * @var float
     *
     * @ORM\Column(name="prixUnitaire", type="float", nullable=true)
     */
    private $prixUnitaire;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", nullable=true)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="typeDevis", type="string", nullable=true)
     */
    private $typeDevis;


    /**
     * @var boolean
     *
     * @ORM\Column(name="enCours", type="boolean", nullable=true)
     */
    private $enCours;


    /**
     * @var boolean
     *
     * @ORM\Column(name="accepter", type="boolean", nullable=true)
     */
    private $accepter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="refuser", type="boolean", nullable=true)
     */
    private $refuser;

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
        $this->expiredAt->modify("+6 months");
        $this->enCours = true;
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
     * Set distance
     *
     * @param float $distance
     *
     * @return Devis
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set duree
     *
     * @param \DateTime $duree
     *
     * @return Devis
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return \DateTime
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Devis
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Devis
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set expiredAt
     *
     * @param string $expiredAt
     *
     * @return Devis
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get expiredAt
     *
     * @return string
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * Set auteur
     *
     * @param \UserBundle\Entity\User $auteur
     *
     * @return Devis
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
     * Set client
     *
     * @param \UserBundle\Entity\User $client
     *
     * @return Devis
     */
    public function setClient(\UserBundle\Entity\User $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \UserBundle\Entity\User
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set accepter
     *
     * @param boolean $accepter
     *
     * @return Devis
     */
    public function setAccepter($accepter)
    {
        $this->accepter = $accepter;

        return $this;
    }

    /**
     * Get accepter
     *
     * @return boolean
     */
    public function getAccepter()
    {
        return $this->accepter;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return Devis
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set typeDevis
     *
     * @param string $typeDevis
     *
     * @return Devis
     */
    public function setTypeDevis($typeDevis)
    {
        $this->typeDevis = $typeDevis;

        return $this;
    }

    /**
     * Get typeDevis
     *
     * @return string
     */
    public function getTypeDevis()
    {
        return $this->typeDevis;
    }

    /**
     * Set annonce
     *
     * @param \AnnonceBundle\Entity\Annonce $annonce
     *
     * @return Devis
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
     * Set prixUnitaire
     *
     * @param float $prixUnitaire
     *
     * @return Devis
     */
    public function setPrixUnitaire($prixUnitaire)
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    /**
     * Get prixUnitaire
     *
     * @return float
     */
    public function getPrixUnitaire()
    {
        return $this->prixUnitaire;
    }


    /**
     * Set refuser
     *
     * @param boolean $refuser
     *
     * @return Devis
     */
    public function setRefuser($refuser)
    {
        $this->refuser = $refuser;

        return $this;
    }

    /**
     * Get refuser
     *
     * @return boolean
     */
    public function getRefuser()
    {
        return $this->refuser;
    }

    /**
     * Set enCours
     *
     * @param boolean $enCours
     *
     * @return Devis
     */
    public function setEnCours($enCours)
    {
        $this->enCours = $enCours;

        return $this;
    }

    /**
     * Get enCours
     *
     * @return boolean
     */
    public function getEnCours()
    {
        return $this->enCours;
    }


    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'Auteur'=>$this->getAuteur()->toArray(),
            'Client'=>$this->getClient()->toArray(),
            'Annonce'=>$this->getAnnonce()->getObject()->toArray(),

            'Distance'=>$this->getDistance(),
            'Duree'=>$this->getDuree()->getTimestamp(),
            'PrixUnitaire'=>$this->getPrixUnitaire(),
            'Montant'=>$this->getMontant(),
            'TypeDevis'=>$this->getTypeDevis(),

            'Accepter'=>$this->getAccepter(),
            'Refuser'=>$this->getRefuser(),
            'EnCours'=>$this->getEnCours(),

            'CreatedAt'=>$this->getCreatedAt()->getTimestamp(),
            'UpdatedAt'=>$this->getUpdatedAt()->getTimestamp(),
            'ExpiredAt'=>$this->getExpiredAt()->getTimestamp()
        );
    }


    
}
