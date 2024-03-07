<?php

/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Core\Configure;
use ADmad\I18n\Routing\Route\I18nRoute;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */

/** @var \Cake\Routing\RouteBuilder $routes */
// $routes->setRouteClass(\ADmad\I18n\Routing\Route\I18nRoute::class);
$routes->setRouteClass(DashedRoute::class);


//Metto questo qui, così capisce che se l'estensione e json voglio il risultato in questo formato
//TODO: In realtà in cake4 devo copiare anche alla riga 65 se no se lo perde! massimoi - 26/3/20
Router::extensions(['xls', 'json']);

if (Configure::check('plugins')) {
  $plugins = Configure::read('AngelCake.plugins');
  if (!empty($plugins)) {
    foreach ($plugins as $p) {
      $routes->loadPlugin($p);
    }
  }
}

$routes->scope('/images', function ($routes) {
  $routes->registerMiddleware('glide', new \ADmad\Glide\Middleware\GlideMiddleware([
    // Run this filter only for URLs starting with specified string. Default null.
    // Setting this option is required only if you want to setup the middleware
    // in Application::middleware() instead of using router's scoped middleware.
    // It would normally be set to same value as that of server.base_url below.
    'path' => null,

    // Either a callable which returns an instance of League\Glide\Server
    // or config array to be used to create server instance.
    // http://glide.thephpleague.com/1.0/config/setup/
    'server' => [
      // Path or League\Flysystem adapter instance to read images from.
      // http://glide.thephpleague.com/1.0/config/source-and-cache/
      'source' => WWW_ROOT,

      // Path or League\Flysystem adapter instance to write cached images to.
      'cache' => CACHE . Configure::read('sitedir'), // WWW_ROOT  . 'cache',

      // URL part to be omitted from source path. Defaults to "/images/"
      // http://glide.thephpleague.com/1.0/config/source-and-cache/#set-a-base-url
      'base_url' => '/images/',

      // Response class for serving images. If unset (default) an instance of
      // \ADmad\Glide\Responses\PsrResponseFactory() will be used.
      // http://glide.thephpleague.com/1.0/config/responses/
      'response' => null,
    ],

    // http://glide.thephpleague.com/1.0/config/security/
    'security' => [
      // Boolean indicating whether secure URLs should be used to prevent URL
      // parameter manipulation. Default false.
      'secureUrls' => false,

      // Signing key used to generate / validate URLs if `secureUrls` is `true`.
      // If unset value of Cake\Utility\Security::salt() will be used.
      'signKey' => null,
    ],

    // Cache duration. Default '+1 days'.
    'cacheTime' => '+1 days',
  ]));

  $routes->applyMiddleware('glide');

  $routes->connect('/*');
});

//Se nel file di configurazione ho specificato customRoutes, allora
  //importo extra-routes.php nella cartella sites/nomesito/
  $er = Configure::read('ExtraRoutes');
  if ($er) {
    include_once conf_path() . DS .  'extra-routes.php';
  }


$routes->scope('/', function (RouteBuilder $builder) {
  $builder->setExtensions(['xls', 'json', 'xml']);

  /*
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, templates/Pages/home.php)...
     */
  $builder->connect('/sitemap', ['plugin' => null, 'controller' => 'Sitemaps', 'action' => 'index']);    


  //$builder->connect('/tlogin', ['plugin' => 'Cyclomap', 'controller' => 'Users', 'action' => 'tlogin']);
  $builder->connect('/', ['plugin' => null, 'controller' => 'Pages', 'action' => 'display', 'home'], ['routeClass' => I18nRoute::class]);
  $builder->connect('/', ['plugin' => null, 'controller' => 'Pages', 'action' => 'display', 'home']);
  $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout'], ['routeClass' => I18nRoute::class]);
  $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
  //$builder->connect('/login', ['controller' => 'Users', 'action' => 'login'], ['routeClass' => I18nRoute::class]);
  $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);

  /*
     * ...and connect the rest of 'Pages' controller's URLs.
     */
  $builder->connect('/pages/*', ['plugin' => null, 'controller' => 'Pages', 'action' => 'display'], ['routeClass' => I18nRoute::class]);
  $builder->connect('/pages/*', ['plugin' => null, 'controller' => 'Pages', 'action' => 'display']);
  /*
     * Connect catchall routes for all controllers.
     *
     * The `fallbacks` method is a shortcut for
     *
     * ```
     * $builder->connect('/:controller', ['action' => 'index']);
     * $builder->connect('/:controller/:action/*', []);
     * ```
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */

  // $builder->fallbacks(\ADmad\I18n\Routing\Route\I18nRoute::class); // TODO: readd this, manually route everything
  // $builder->fallbacks(DashedRoute::class);

  
});

$routes->prefix('Admin', function (RouteBuilder $routes) { // Admin routes
  // All routes here will be prefixed with `/admin`, and
  // have the `'prefix' => 'Admin'` route element added that
  // will be required when generating URLs for these routes
  $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'admin'], ['routeClass' => I18nRoute::class]);
  $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'admin']);
  $routes->connect('/articles', ['controller' => 'Articles', 'action' => 'index'], ['routeClass' => I18nRoute::class]);
  $routes->connect('/articles', ['controller' => 'Articles', 'action' => 'index']); // TODO this fixes Poi, Percorsi, Destinations?
  $routes->setExtensions(['xls', 'json']);
  // Admin fallbacks
  $routes->fallbacks(I18nRoute::class);
  $routes->fallbacks(DashedRoute::class);
});

$routes->scope('/', function (RouteBuilder $builder) { // Last, build generic fallbacks
  $builder->setExtensions(['xls', 'json']);
  $builder->fallbacks(I18nRoute::class);
  $builder->fallbacks(DashedRoute::class);
});

/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */

/*  $routes->plugin('Ebike2021', ['path' => 'ebike'], function (RouteBuilder $routes) {
   $routes->connect('/tlogin', ['controller' => 'Users', 'action' => 'tlogin']);
 });

 */
