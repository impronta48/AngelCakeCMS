<?php
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
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\I18n\I18n;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        
        // Important: add the 'enableBeforeRedirect' config or or disable deprecation warnings
        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');                
        $this->loadComponent('CakeDC/Users.UsersAuth');
        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');        

        $config['Auth']['authorize']['CakeDC/Users.SimpleRbac'] = [
        // autoload permissions.php
        'autoload_config' => 'permissions',
        // role field in the Users table
        'role_field' => 'role',
        // default role, used in new users registered and also as role matcher when no role is available
        'default_role' => 'user',
        // log will default to the 'debug' value, matched rbac rules will be logged in debug.log by default when debug enabled
        'log' => false
    ];

        $this->Auth->allow('display');
    }

    public function beforeRender(\Cake\Event\Event $event)
    {          
        if (strpos($this->request->getRequestTarget(),'/en') !== false) {
            I18n::setLocale('en');
        }elseif (strpos($this->request->getRequestTarget(),'/es') !== false ){
            I18n::setLocale('es');
        }
        $this->set('locale',  substr(I18n::getLocale(),0,2));

        if (Configure::check('theme')){
            $this->viewBuilder()->setTheme(Configure::read('theme'));
        }
    }


}
