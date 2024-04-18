<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avis
 *
 * @ORM\Table(name="user_avis")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\AvisRepository")
 */
class Avis
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="auteurAvis", cascade={"persist"})
     * @ORM\JoinColumn(name="auteur_id", referencedColumnName="id")
     */
    private $auteur;

    /**
     * @var user
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="clientAvis", cascade={"persist"})
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;


    /**
     * @var annonce
     *
     * @ORM\ManyToOne(targetEntity="AnnonceBundle\Entity\Annonce", inversedBy="avis", cascade={"persist"})
     * @ORM\JoinColumn(name="annonce_id", referencedColumnName="id")
     */
    private $annonce;

    /**
     * @var integer
     *
     * @ORM\Column(name="Note", type="integer")
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="Message", type="text")
     */
    private $message;

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


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * Set message
     *
     * @param string $message
     *
     * @return Avis
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
     * Set note
     *
     * @param integer $note
     *
     * @return Avis
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set client
     *
     * @param \UserBundle\Entity\User $client
     *
     * @return Avis
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
     * Set auteur
     *
     * @param \UserBundle\Entity\User $auteur
     *
     * @return Avis
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
     * Set annonce
     *
     * @param \AnnonceBundle\Entity\Annonce $annonce
     *
     * @return Avis
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


    public function toArray(){
        return array(
            "Id"=>$this->getId(),
            "Auteur"=>$this->getAuteur()->toArray(),
            "Client"=>$this->getClient()->toArray(),
            // "Annonce"=>$this->getAnnonce()->toArray(),
            "Note"=>$this->getNote(),
            "Message"=>$this->getMessage(),
            "CreatedAt"=>$this->getCreatedAt()->getTimestamp(),
            "UpdatedAt"=>$this->getUpdatedAt()->getTimestamp()
        );
    }
}
