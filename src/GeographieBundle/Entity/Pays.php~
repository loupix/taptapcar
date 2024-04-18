<?php

namespace GeographieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lyssal\GeographieBundle\Entity\Pays as BasePays;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Pays du monde.
 * 
 * @ORM\Entity()
 * @ORM\Table
 * (
 *     name="geo_pays",
 *     uniqueConstraints=
 *     {
 *         @UniqueConstraint(name="CODE_ALPHA_2", columns={ "pays_code_alpha_2" }),
 *         @UniqueConstraint(name="CODE_ALPHA_3", columns={ "pays_code_alpha_3" })
 *     }
 * )
 */
class Pays extends BasePays
{
    /**
     * @var array<\GeographieBundle\Entity\Region>
     * 
     * @ORM\OneToMany(targetEntity="Region", mappedBy="pays")
     */
    protected $regions;
}
