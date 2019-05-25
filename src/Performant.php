<?php

namespace PeteKlein\Performant;

use PeteKlein\Performant\Registration\Registrar;

class Performant
{
    public static function init()
    {
        Registrar::register();
    }
}
