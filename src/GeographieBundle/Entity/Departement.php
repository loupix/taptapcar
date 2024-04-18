<?php

namespace GeographieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lyssal\GeographieBundle\Entity\Departement as BaseDepartement;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Département d'une région.
 * 
 * @ORM\Entity(repositoryClass="\GeographieBundle\Repository\DepartementRepository")
 * @ORM\Table
 * (
 *     name="geo_departement",
 *     uniqueConstraints=
 *     {
 *         @UniqueConstraint(name="REGION_CODE", columns={ "region_id", "departement_code" })
 *     }
 * )
 */
class Departement extends BaseDepartement
{
    /**
     * @var Region
     * 
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="departements")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="region_id", nullable=false, onDelete="CASCADE")
     */
    protected $region;

    /**
     * @var array<\GeographieBundle\Entity\Ville>
     * 
     * @ORM\OneToMany(targetEntity="Ville", mappedBy="departement")
     */
    protected $villes;


    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'Nom'=>$this->getNom(),
            'Slug'=>$this->getSlug()
            // 'Region'=>$this->getRegion()->,

        );
    }
}
