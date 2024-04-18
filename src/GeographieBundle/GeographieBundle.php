<?php

namespace GeographieBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GeographieBundle extends Bundle
{
	public function getParent()
    {
        return 'LyssalGeographieBundle';
    }
}
