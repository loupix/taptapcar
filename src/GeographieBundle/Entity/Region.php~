<?php

namespace GeographieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lyssal\GeographieBundle\Entity\Region as BaseRegion;

/**
 * Région d'un pays.
 * 
 * @ORM\Entity()
 * @ORM\Table(name="geo_region")
 */
class Region extends BaseRegion
{

    /**
     * @ORM\OneToMany(targetEntity="Departement", mappedBy="region")
     */
    protected $departements;


    /**
     * @var Pays
     * 
     * @ORM\ManyToOne(targetEntity="Pays", inversedBy="regions")
     * @ORM\JoinColumn(name="pays_id", referencedColumnName="pays_id", nullable=false, onDelete="CASCADE")
     */
    protected $pays;
    
}
