<?php
namespace App\View;

use BootstrapUI\View\UIViewTrait;
use Cake\View\View;

class AppView extends View
{
    use UIViewTrait;

    /**
     * Initialization hook method.
     */
    public function initialize()
    {    	
        //render the initializeUI method from the UIViewTrait
         $this->initializeUI(['layout' => false]);
         $this->loadHelper('CakeDC/Users.User');
         $this->loadHelper('Paginator');
    }
}