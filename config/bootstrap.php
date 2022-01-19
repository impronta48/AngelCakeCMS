<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Cache\Engine\FileEngine;
use Cake\Cache\Engine\MemcachedEngine;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ConsoleErrorHandler;
use Cake\Error\ErrorHandler;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\Utility\Inflector;
use Cake\Database\Type;

/*
 * See https://github.com/josegonzalez/php-dotenv for API details.
 *
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.example` to `config/.env` and set/modify the
 * variables as required.
 *
 * The purpose of the .env file is to emulate the presence of the environment
 * variables like they would be present in production.
 *
 * If you use .env files, be careful to not commit them to source control to avoid
 * security risks. See https://github.com/josegonzalez/php-dotenv#general-security-information
 * for more information for recommended practices.
*/
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }


/*
 * Define default auth roles (just admin)
 */
define('ROLE_ADMIN', 1);
define('ROLE_USER', 5);
define('ADMIN_ROLES_LIST', [1]);

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
/*Carico il file di configurazione specifico di questo dominio*/

$path = conf_path();
Configure::write('confPath', $path);
try {
  Configure::config('default', new PhpConfig());
  Configure::load('app', 'default', false);
  //echo  CONFIG . $path; die;
  Configure::config('special', new PhpConfig(CONFIG . $path . DS));
  Configure::load("settings", 'special');
} catch (\Exception $e) {
  exit($e->getMessage() . "\n");
}

// Cache::setConfig(Configure::consume('Cache'));
/*
 * Configure generic caches.
 */
Cache::setConfig('default', [
  'className' => FileEngine::class,
  'path' => CACHE . Configure::read('sitedir') . DS,
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
  // 'className' => MemcachedEngine::class,
  'serialize' => true,
  //'prefix' => 'angelcake_routes_' . Configure::read('sitedir') . '_',
  'className' => FileEngine::class,
  'path' =>  CACHE . Configure::read('sitedir') . DS . 'routes' . DS,
  'serialize' => 'php',
  'duration' => '+1 week',
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

//Carico la configurazione specifica per il filesystem
//il plugin di riferimento è https://github.com/josbeir/cakephp-filesystem
//Massimoi - 3/4/2020
Configure::load('filesystems', 'default');

//Se nel settings definisci la variabile THEME allora carico il tema qui
if (Configure::check('theme')) {
  $this->addPlugin(Configure::read('theme'), ['routes' => true]);
}

/*
 * Load an environment local configuration file to provide overrides to your configuration.
 * Notice: For security reasons app_local.php **should not** be included in your git repo.
 */
/* if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}*/

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
  Configure::write('Cache._cake_model_.duration', '+2 minutes');
  Configure::write('Cache._cake_core_.duration', '+2 minutes');
  Configure::write('Cache._cake_routes_.duration', '+2 minutes');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
  (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
  (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
  require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 */
$fullBaseUrl = Configure::read('App.fullBaseUrl');
if (!$fullBaseUrl) {
  $s = null;
  if (env('HTTPS')) {
    $s = 's';
  }

  $httpHost = env('HTTP_HOST');
  if (isset($httpHost)) {
    $fullBaseUrl = 'http' . $s . '://' . $httpHost;
  }
  unset($httpHost, $s);
}
if ($fullBaseUrl) {
  Router::fullBaseUrl($fullBaseUrl);
}
unset($fullBaseUrl);

ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Mailer::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
  $detector = new \Detection\MobileDetect();

  return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
  $detector = new \Detection\MobileDetect();

  return $detector->isTablet();
});

/*
 * You can set whether the ORM uses immutable or mutable Time types.
 * The default changed in 4.0 to immutable types. You can uncomment
 * below to switch back to mutable types.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/4/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
// TypeFactory::build('time')
//    ->useMutable();
// TypeFactory::build('date')
//    ->useMutable();
//TypeFactory::build('datetime')
//        ->useMutable();
// TypeFactory::build('timestamp')
//    ->useMutable();
// TypeFactory::build('datetimefractional')
//    ->useMutable();
// TypeFactory::build('timestampfractional')
//    ->useMutable();
// TypeFactory::build('datetimetimezone')
//    ->useMutable();
// TypeFactory::build('timestamptimezone')
//    ->useMutable();

//Necessario per gestire i campi datetime senza specificare i secondi
TypeFactory::build('datetime')->useLocaleParser()->setLocaleFormat('yyyy-MM-dd\'T\'HH:mm');
Type::map('json', 'Cake\Database\Type\JsonType');
//TypeFactory::build('date')->useLocaleParser()->setLocaleFormat('yyyy-MM-dd');

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);
//require_once 'events.php';

// Get the API whitelist. If this is empty, all requests will have CORS enabled
$api_whitelist = Configure::read('api-whitelist');

// header('Access-Control-Allow-Methods: POST, GET, PUT, PATCH, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Accept, Accept-Encoding, Accept-Language');
// header('Access-Control-Allow-Type: application/json');

if (isset($_SERVER['HTTP_ORIGIN']) && (empty($api_whitelist) || in_array($_SERVER['HTTP_ORIGIN'], $api_whitelist))) {
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
  header('Access-Control-Allow-Origin:');
}

if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
  header("Access-Control-Allow-Methods: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']}, OPTIONS");
} else {
  header('Access-Control-Allow-Methods: *');
}

if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
} else {
  header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Accept, Accept-Encoding, Accept-Language');
}

header('Access-Control-Allow-Credentials: true');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  exit(0);
}

Configure::write('phpFileUploadErrors', [
  0 => 'There is no error, the file uploaded with success',
  1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini: ' . ini_get("upload_max_filesize"),
  2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
  3 => 'The uploaded file was only partially uploaded',
  4 => 'No file was uploaded',
  6 => 'Missing a temporary folder',
  7 => 'Failed to write file to disk.',
  8 => 'A PHP extension stopped the file upload.',
]);

Configure::write('groups', [
  1 => 'admin',
  2 => 'editor',
  3 => 'user'
]);
