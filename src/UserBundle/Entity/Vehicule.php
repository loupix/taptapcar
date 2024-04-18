<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicule
 *
 * @ORM\Table(name="user_vehicule")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\VehiculeRepository")
 */
class Vehicule
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
     * @var profile
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Profile", inversedBy="vehicule")
     */
    private $profile;


    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="Modele", type="string", length=255, nullable=true)
     */
    private $modele;

    /**
     * @var string
     *
     * @ORM\Column(name="Marque", type="string", length=255, nullable=true)
     */
    private $marque;

    /**
     * @var int
     *
     * @ORM\Column(name="kilometrage", type="bigint", nullable=true)
     */
    private $kilometrage;

    /**
     * @var int
     *
     * @ORM\Column(name="places", type="smallint", nullable=true)
     */
    private $places;


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
     * Set type
     *
     * @param string $type
     *
     * @return Vehicule
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
     * Set marque
     *
     * @param string $marque
     *
     * @return Vehicule
     */
    public function setMarque($marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque
     *
     * @return string
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set kilometrage
     *
     * @param integer $kilometrage
     *
     * @return Vehicule
     */
    public function setKilometrage($kilometrage)
    {
        $this->kilometrage = $kilometrage;

        return $this;
    }

    /**
     * Get kilometrage
     *
     * @return int
     */
    public function getKilometrage()
    {
        return $this->kilometrage;
    }

    /**
     * Set places
     *
     * @param integer $places
     *
     * @return Vehicule
     */
    public function setPlaces($places)
    {
        $this->places = $places;

        return $this;
    }

    /**
     * Get places
     *
     * @return int
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * Set profile
     *
     * @param \UserBundle\Entity\Profile $profile
     *
     * @return Vehicule
     */
    public function setProfile(\UserBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \UserBundle\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set modele
     *
     * @param string $modele
     *
     * @return Vehicule
     */
    public function setModele($modele)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get modele
     *
     * @return string
     */
    public function getModele()
    {
        return $this->modele;
    }



    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'Type'=>$this->getType(),
            'Modele'=>$this->getModele(),
            'Marque'=>$this->getMarque(),
            'Kilometrage'=>$this->getKilometrage(),
            'Places'=>$this->getPlaces(),
        );
    }
}
