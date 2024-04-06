<?php 
// Cache::setConfig(Configure::consume('Cache'));
/*
 * Configure generic caches.
 */

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Cache\Engine\FileEngine;
use Cake\Cache\Engine\MemcachedEngine;

Cache::setConfig('default', [
  'className' => FileEngine::class,
  'path' => CACHE . Configure::read('sitedir') . DS,
]);
Cache::setConfig('long', [
  'className' => FileEngine::class,
  'path' => CACHE . Configure::read('sitedir') . DS,
  'duration' => '+1 months',
]);
Cache::setConfig('static', [
  'className' => FileEngine::class,
  'path' => CACHE . Configure::read('sitedir') . DS . 'static' . DS,
]);
/*
 * Configure the cache used for general framework caching.
 * Translation cache files are stored with this configuration.
 * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
 * If you set 'className' => 'Null' core cache will be disabled.
 */
Cache::setConfig('_cake_core_', [
  'className' => FileEngine::class,
  'prefix' => 'angelcake_core_',
  'path' =>  CACHE . Configure::read('sitedir') . DS . 'persistent' . DS,
  'serialize' => true,
  'duration' => '+1 years',
]);
/*
 * Configure the cache for model and datasource caches. This cache
 * configuration is used to store schema descriptions, and table listings
 * in connections.
 * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
 */
Cache::setConfig('_cake_model_', [
  'className' => FileEngine::class,
  'prefix' => 'angelcake_model_',
  'path' =>  CACHE . Configure::read('sitedir') . DS . 'models' . DS,
  'serialize' => true,
  'duration' => '+1 years',
]);
/*
 * Configure the cache for routes. The cached routes collection is built the
 * first time the routes are processed through `config/routes.php`.
 * Duration will be set to '+2 seconds' in bootstrap.php when debug = true
 */
Cache::setConfig('_cake_routes_', [
  'className' => FileEngine::class,  
  'path' => CACHE . Configure::read('sitedir') . DS . 'routes' . DS,
  'duration' => '+20 days',
  'serialize' => 'json',
  //'url' => env('CACHE_CAKEROUTES_URL', null),
]);
/*
 * Configure images cache.
 */
Cache::setConfig('img', [
  'className' => 'File',
  'prefix' => 'angelcake_img_',
  'path' =>  CACHE . Configure::read('sitedir') . DS . 'gallery' . DS,
  'serialize' => true,
  'duration' => '+1 years',
]);
