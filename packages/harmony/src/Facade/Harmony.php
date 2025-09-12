<?php

namespace Harmony\Facade;

use Illuminate\Support\Facades\Facade;

class Harmony extends Facade
{
     protected static function getFacadeAccessor()
     {
          return 'harmony';
     }
}