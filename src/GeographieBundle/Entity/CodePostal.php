<?php

namespace GeographieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lyssal\GeographieBundle\Entity\CodePostal as BaseCodePostal;

/**
 * Code postal.
 * 
 * @ORM\Entity()
 * @ORM\Table(name="geo_code_postal", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE_VILLE_CODE", columns={"ville_id", "code_postal_code"})})
 */
class CodePostal extends BaseCodePostal
{
	
}
