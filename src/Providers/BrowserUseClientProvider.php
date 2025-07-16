<?php 

namespace KepsonDiaz\Providers;

use Illuminate\Support\ServiceProvider;
use KepsonDiaz\Manager\BrowserUseHttpClientManager;

class BrowserUseClientProvider extends ServiceProvider
{
   public function register()
   {
      $this->app->singleton('BrowserUseHttpClient', function ($app) {
         return new BrowserUseHttpClientManager($app['config']['browser-use.api_key']);
      });
   }
}