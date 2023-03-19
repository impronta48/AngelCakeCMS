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
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\ResolverCollection;
use Authorization\Policy\MapResolver;
use Authorization\Policy\OrmResolver;
use Psr\Http\Message\ResponseInterface;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use ADmad\SocialAuth\Middleware\SocialAuthMiddleware;
use Authorization\Middleware\RequestAuthorizationMiddleware;
use App\Policy\RequestPolicy;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Cake\Http\ServerRequest;
use Fetzi\ServerTiming\ServerTimingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface, AuthorizationServiceProviderInterface
{
  /**
   * Load all the application configuration and bootstrap logic.
   *
   * @return void
   */
  public function bootstrap(): void
  {
    $this->addPlugin('DebugKit');

    $this->addPlugin('AssetMix');

    // Call parent to load bootstrap from files.
    parent::bootstrap();

    if (PHP_SAPI === 'cli') {
      $this->bootstrapCli();
    }

    /*
		 * Only try to load DebugKit in development mode
		 * Debug Kit should not be installed on a production system
		 */
    //Configure::write('DebugKit.forceEnable', true);
    if (Configure::read('debug')) {
      $this->addPlugin('DebugKit');
    }

    // Load more plugins here
    $this->addPlugin('Authentication');
    $this->addPlugin('Authorization');
    $this->addPlugin('BootstrapUI');
    $this->addPlugin('ADmad/Glide');
    $this->addPlugin('ADmad/SocialAuth');
    //$this->addPlugin('Notifications');
    

    if (Configure::check('AngelCake.plugins')) {
      $plugins = Configure::read('AngelCake.plugins');
      if (!empty($plugins)) {
        foreach ($plugins as $p) {
          $this->addPlugin($p, ['routes' => false, 'bootstrap' => true]);
        }
      }
    }
      $this->addPlugin('Satispay');
  }

  /**
   * Setup the middleware queue your application will use.
   *
   * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
   * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
   */
  public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
  {

    $middlewareQueue
      // Catch any exceptions in the lower layers,
      // and make an error page/response
      ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

      // Handle plugin/theme assets like CakePHP normally does.
      ->add(new AssetMiddleware([
        'cacheTime' => Configure::read('Asset.cacheTime'),
      ]))

      // Add routing middleware.
      // If you have a large number of routes connected, turning on routes
      // caching in production could improve performance. For that when
      // creating the middleware instance specify the cache config name by
      // using it's second constructor argument:
      //->add(new RoutingMiddleware($this))
      ->add(new RoutingMiddleware($this, '_cake_routes_'))

      // Needed to specify locale associations, because we're using 3 letter locales (non-default)
      ->add(new \ADmad\I18n\Middleware\I18nMiddleware([
        // If `true` will attempt to get matching languges in "languages" list based
        // on browser locale and redirect to that when going to site root.
        // 'detectLanguage' => true,
        // Default language for app. If language detection is disabled or no
        // matching language is found redirect to this language
        'defaultLanguage' => 'ita',
        // Languages available in app. The keys should match the language prefix used
        // in URLs. Based on the language the locale will be also set.
        'languages' => [
          'eng' => ['locale' => 'eng'],
          'ita' => ['locale' => 'ita'],
        ],
      ]));

    // Be sure to add SocialAuthMiddleware after RoutingMiddleware
    $middlewareQueue->add(new SocialAuthMiddleware([
      // Request method type use to initiate authentication.
      'requestMethod' => 'POST',
      // Login page URL. In case of auth failure user is redirected to login
      // page with "error" query string var.
      'loginUrl' => '/users/login',
      // URL to redirect to after authentication (string or array).
      'loginRedirect' => '/admin/',
      // Boolean indicating whether user identity should be returned as entity.
      'userEntity' => false,
      // User model.
      'userModel' => 'Users',
      // Social profile model.
      'socialProfileModel' => 'ADmad/SocialAuth.SocialProfiles',
      // Finder type.
      'finder' => 'all',
      // Fields.
      'fields' => [
        'password' => 'password',
      ],
      // Session key to which to write identity record to.
      'sessionKey' => 'Auth',
      // The method in user model which should be called in case of new user.
      // It should return a User entity.
      'getUserCallback' => 'getUser',
      // SocialConnect Auth service's providers config. https://github.com/SocialConnect/auth/blob/master/README.md
      'serviceConfig' => [
        'provider' => [
          'facebook' => [
            'applicationId' => Configure::read('Facebook.appid'),
            'applicationSecret' => Configure::read('Facebook.secret'),
            'scope' => [
              'email',
            ],
            'options' => [
              'identity.fields' => [
                'email',
                'first_name',
                'last_name',
                'name',
                'picture',
                'short_name',
                // To get a full list of all possible values, refer to
                // https://developers.facebook.com/docs/graph-api/reference/user
              ],
            ],
          ],
          'google' => [
            'applicationId' => Configure::read('Google.appid'),
            'applicationSecret' => Configure::read('Google.secret'),
            'scope' => [
              'https://www.googleapis.com/auth/userinfo.email',
              'https://www.googleapis.com/auth/userinfo.profile',
            ],
          ],
        ],
      ],
      // Whether social connect errors should be logged. Default `true`.
      'logErrors' => true,
    ]));

    $middlewareQueue->add(new AuthenticationMiddleware($this));
    $middlewareQueue->add(new AuthorizationMiddleware($this, [
      'unauthorizedHandler' => [
        'className' => 'CustomRedirect', // <--- see here
        'url' => '/users/login',
        'queryParam' => 'redirectUrl',
        'exceptions' => [
          MissingIdentityException::class,
          ForbiddenException::class
        ],
        'custom_param' => true,
      ],
      // 'requireAuthorizationCheck' => false,
      // 'identityDecorator' => function ($auth, $user) {
      //   return $user->setAuthorization($auth);
      // }
    ]));
    // $middlewareQueue->add(new RequestAuthorizationMiddleware());

    $middlewareQueue->add(new EncryptedCookieMiddleware(
      ['secrets', 'protected'],
      "PASSWORDmoltoDifficile1234"
    ));    

    // Add your middlewares here
    if (Configure::read('debug')) {
      // Disable authz for debugkit
      $middlewareQueue->add(function ($req, $res, $next) {
        if ($req->getParam('plugin') === 'DebugKit') {
          $req->getAttribute('authorization')->skipAuthorization();
        }
        return $next($req, $res);
      });
    }
    //->add(new LocaleSelectorMiddleware());

    return $middlewareQueue;
  }

  /**
   * Bootrapping for CLI application.
   *
   * That is when running commands.
   *
   * @return void
   */
  protected function bootstrapCli(): void
  {
    try {
      $this->addPlugin('Bake');
    } catch (MissingPluginException $e) {
      // Do not halt if the plugin is missing
    }

    $this->addPlugin('Migrations');

    // Load more plugins here
  }

  /**
   * Returns a service provider instance.
   *
   * @param \Psr\Http\Message\ServerRequestInterface $request Request
   * @return \Authentication\AuthenticationServiceInterface
   */
  public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
  {
    $service = new AuthenticationService();

    // Define where users should be redirected to when they are not authenticated
    $service->setConfig([
      'unauthenticatedRedirect' => Router::url([
        'prefix' => false,
        'plugin' => null,
        'controller' => 'Users',
        'action' => 'login',
      ]),
      'queryParam' => 'redirect',
    ]);

    $fields = [
      IdentifierInterface::CREDENTIAL_USERNAME => 'username',
      IdentifierInterface::CREDENTIAL_PASSWORD => 'password'
    ];
    // Load the authenticators. Session should be first.
    $service->loadAuthenticator('Authentication.Session');
    $service->loadAuthenticator('Authentication.Form', [
      'fields' => $fields,
      'loginUrl' => Router::url([
        'prefix' => false,
        'plugin' => null,
        'controller' => 'Users',
        'action' => 'login',
      ]),
    ]);

    // Load identifiers
    $service->loadIdentifier('Authentication.Password', compact('fields'));
    
    // If the user is on the login page, check for a cookie as well.
    $service->loadAuthenticator('Authentication.Cookie', [
      'fields' => $fields,
      'loginUrl' => '/users/login',
    ]);

    return $service;
  }

  public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
  {
    $ormResolver = new OrmResolver();
    $mapResolver = new MapResolver();

    // $mapResolver->map(ServerRequest::class, RequestPolicy::class);
      
    // Check the map resolver, and fallback to the orm resolver if
    // a resource is not explicitly mapped.
    $resolver = new ResolverCollection([$mapResolver, $ormResolver]);

    return new AuthorizationService($resolver);
  }

}
