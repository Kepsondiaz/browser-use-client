<?php

namespace KepsonDiaz\Facades;

use Illuminate\Support\Facades\Facade;

class BrowserUseHttpClient extends Facade
{
   protected static function getFacadeAccessor()
   {
      return 'BrowserUseHttpClient';
   }
}