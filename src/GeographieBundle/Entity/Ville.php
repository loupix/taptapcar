<?php

namespace GeographieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lyssal\GeographieBundle\Entity\Ville as BaseVille;

/**
 * Ville.
 * 
 * @ORM\Entity(repositoryClass="GeographieBundle\Repository\VilleRepository")
 * @ORM\Table(name="geo_ville")
 */
class Ville extends BaseVille
{
    /**
     * @var \GeographieBundle\Entity\Departement
     * 
     * @ORM\ManyToOne(targetEntity="Departement", inversedBy="villes")
     * @ORM\JoinColumn(name="departement_id", referencedColumnName="departement_id", nullable=false, onDelete="CASCADE")
     */
    protected $departement;

    /**
     * @var place
     *
     * @ORM\OneToMany(targetEntity="Place", mappedBy="ville", cascade={"remove", "persist"})
     */
    private $places;


    /**
     * @var \string
     *
     * @ORM\Column(name="street_number", type="string", nullable=true)
     */
    private $street_number;


    /**
     * @var \string
     *
     * @ORM\Column(name="route", type="string", nullable=true)
     */
    private $route;


    /**
     * @var \string
     *
     * @ORM\Column(name="locality", type="string", nullable=true)
     */
    private $locality;


    /**
     * @var \string
     *
     * @ORM\Column(name="administrative_area_level_1", type="string", nullable=true)
     */
    private $administrative_area_level_1;


    /**
     * @var \string
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;


    /**
     * @var \string
     *
     * @ORM\Column(name="postal_code", type="string", nullable=true)
     */
    private $postal_code;

    /**
     * Add codePostaux
     *
     * @param \GeographieBundle\Entity\CodePostal $codePostaux
     *
     * @return Ville
     */
    public function addCodePostaux(\GeographieBundle\Entity\CodePostal $codePostaux)
    {
        $this->codePostaux[] = $codePostaux;

        return $this;
    }

    /**
     * Add place
     *
     * @param \GeographieBundle\Entity\Place $place
     *
     * @return Ville
     */
    public function addPlace(\GeographieBundle\Entity\Place $place)
    {
        $this->places[] = $place;

        return $this;
    }

    /**
     * Remove place
     *
     * @param \GeographieBundle\Entity\Place $place
     */
    public function removePlace(\GeographieBundle\Entity\Place $place)
    {
        $this->places->removeElement($place);
    }

    /**
     * Get places
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * Set streetNumber
     *
     * @param string $streetNumber
     *
     * @return Ville
     */
    public function setStreetNumber($streetNumber)
    {
        $this->street_number = $streetNumber;

        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->street_number;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return Ville
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return Ville
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set administrativeAreaLevel1
     *
     * @param string $administrativeAreaLevel1
     *
     * @return Ville
     */
    public function setAdministrativeAreaLevel1($administrativeAreaLevel1)
    {
        $this->administrative_area_level_1 = $administrativeAreaLevel1;

        return $this;
    }

    /**
     * Get administrativeAreaLevel1
     *
     * @return string
     */
    public function getAdministrativeAreaLevel1()
    {
        return $this->administrative_area_level_1;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Ville
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Ville
     */
    public function setPostalCode($postalCode)
    {
        $this->postal_code = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }


    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'Nom'=>$this->getNom(),
            'Slug'=>$this->getSlug(),
            'Gentile'=>$this->getGentile(),
            'Latitude'=>$this->getLatitude(),
            'Longitude'=>$this->getLongitude(),
            'CodePostal'=>$this->getCodePostal(),
            'Departement'=>$this->getDepartement()->toArray(),
            "Locality"=>$this->getLocality(),
            "StreetNumber"=>$this->getStreetNumber(),
            "AdministrativeAreaLevel1"=>$this->getAdministrativeAreaLevel1(),
            "Country"=>$this->getCountry(),
            "PostalCode"=>$this->getPostalCode(),
            "Route"=>$this->getRoute(),
            "Country"=>$this->getCountry()
        );
    }
}
