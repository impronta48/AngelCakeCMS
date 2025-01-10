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
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieInterface;
use Cake\I18n\I18n;
use DateTime;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
  /**
   * Initialization hook method.
   *
   * Use this method to add common initialization code like loading components.
   *
   * e.g. `$this->loadComponent('FormProtection');`
   *
   * @return void
   */
  public function initialize(): void
  {
    parent::initialize();

    $this->loadComponent('RequestHandler');
    $this->loadComponent('Flash');

    /*
		 * Enable the following component for recommended CakePHP form protection settings.
		 * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
		 */
    //$this->loadComponent('FormProtection');

    $this->loadComponent('Authorization.Authorization');
    $this->loadComponent('Authentication.Authentication');

    if (!empty($this->request->getQuery('ref_id'))) {
      $this->response = $this->response->withCookie(Cookie::create(
        'ref_id',
        $this->request->getQuery('ref_id'),
        [
          'expires' => new DateTime('+1 week'),
          'domain' => '.bikesquare.eu',
          'samesite' => CookieInterface::SAMESITE_LAX,
          'secure' => false
        ]
      ));
    }

		$user = $this->request->getAttribute('identity');
    $this->set('auth', $user);

    //This way i load a different layout and I request authentication just for admin/
    if ($this->request->getParam('prefix') === 'Admin') {
		  if (! $user || !in_array($user->group_id, [ROLE_BAM, ROLE_EON, ROLE_ADMIN, ROLE_EDITOR, ROLE_RENTER, ROLE_EON_PLUS, ROLE_COMMERCIALE])) { // kinda raw, there's probably a nicer way, TODO
        $this->Flash->error('Questo utente non è autorizzato ad accedere ad Admin');
		  	$this->redirect('/');
		  } else {
        $this->viewBuilder()->setLayout('admin');
      }
    } else {
      $this->Authentication->allowUnauthenticated(['index', 'view', 'display', 'login', 'logout', 'options']);
      $this->Authorization->skipAuthorization();
    }

   
  }

  public function beforeRender(\Cake\Event\EventInterface $event)
  {
    $locale = I18n::getLocale();
    $this->set('lang', $locale);

    $test_path = APP ;

    $plugin = $this->getPlugin();

    if (!is_null($plugin)) {
      $test_path .= 'plugins' . DS . $plugin ;
    }

    $test_path .= 'templates' . DS;

    $template = $this->viewBuilder()->getTemplate();
    $template_path = $this->viewBuilder()->getTemplatePath();

    $test_path .= $template_path . DS;

    if (file_exists($test_path . $locale . DS . $template)) {
      $this->viewBuilder()->setTemplatePath($template_path . DS . $locale);
    }

    if (Configure::check('theme')) {
      $this->viewBuilder()->setTheme(Configure::read('theme'));
    }
  }
}
