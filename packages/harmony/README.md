## Harmony
A Laravel API package for managing 3rd party API configuration

## Prerequisite
1. If you don't have Guzzle installed, you may run 
   
        composer require guzzlehttp/guzzle

## Installation
1. Download this repo and put into project's root directory under packages/harmony
2. Add to the end of composer.json
   
        "repositories": [
             {
                 "type": "path",
                 "url": "packages/harmony",
                 "options": {
                     "symlink": false
                 }
             }
         ]
3.  Run
   
        composer require aisyahhanifiahrv/harmony:dev-master

4. In AppServiceProvider.php
         
        use Harmony\Harmony;

        public function register(): void
        {
                $this->app->bind('harmony',function(){
                        return new Harmony();
                });
        }

## How To Use
1. To make harmony class
   
        php artisan make:harmony ArticleHarmony

2. To make API request
   
        $response = Harmony::send(new ArticleHarmony()->update($article), $body, $query);