<?php

namespace GeographieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Place
 *
 * @ORM\Table(name="geo_place")
 * @ORM\Entity(repositoryClass="GeographieBundle\Repository\PlaceRepository")
 */
class Place
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
     * @ORM\Column(name="place_id", type="text")
     */
    private $place_id;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="text", nullable=true)
     */
    private $locality;

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="text", nullable=true)
     */
    private $street_number;


    /**
     * @var string
     *
     * @ORM\Column(name="route", type="text", nullable=true)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="administrative_area_level_1", type="text", nullable=true)
     */
    private $administrative_area_level_1;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="text", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="text", nullable=true)
     */
    private $postal_code;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse", type="text")
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="Latitude", type="decimal", precision=10, scale=8)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="Longitude", type="decimal", precision=10, scale=8)
     */
    private $longitude;

    /**
     * @var ville
     *
     * @ORM\ManyToOne(targetEntity="Ville", inversedBy="places", cascade={"persist"})
     * @ORM\JoinColumn(name="ville_id", referencedColumnName="ville_id")
     */
    private $ville;


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
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Place
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Place
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Place
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Place
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
     * Set placeId
     *
     * @param string $placeId
     *
     * @return Place
     */
    public function setPlaceId($placeId)
    {
        $this->place_id = $placeId;

        return $this;
    }

    /**
     * Get placeId
     *
     * @return string
     */
    public function getPlaceId()
    {
        return $this->place_id;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return Place
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
     * @return Place
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
     * @return Place
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
     * @return Place
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


    /**
     * Set streetNumber
     *
     * @param string $streetNumber
     *
     * @return Place
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
     * @return Place
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
     * ToString.
     *
     * @return string Nom de la ville
     */
    public function __toString()
    {
        return $this->getAdresse();
    }



    public function toArray(){
        $arr = array(
            "Id"=>$this->getId(),
            "Adresse"=>$this->getAdresse(),
            "Latitude"=>$this->getLatitude(),
            "Longitude"=>$this->getLongitude(),
            "PlaceId"=>$this->getPlaceId(),
            "Locality"=>$this->getLocality(),
            "StreetNumber"=>$this->getStreetNumber(),
            "AdministrativeAreaLevel1"=>$this->getAdministrativeAreaLevel1(),
            "Country"=>$this->getCountry(),
            "PostalCode"=>$this->getPostalCode(),
            "Route"=>$this->getRoute(),
            "Country"=>$this->getCountry()
        );
        if($this->getVille())
            $arr["Ville"] = $this->getVille()->toArray();
        return $arr;
    }

    

    

    
}
