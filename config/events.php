<?php
use CakeDC\Users\Controller\Component\UsersAuthComponent;
use Cake\Event\Event;
use Cake\Event\EventManager;

EventManager::instance()->on(
    UsersAuthComponent::EVENT_AFTER_LOGIN,
    ['priority' => 99], // set last in the priority queue in case you add more events to AFTER_LOGIN
    function (Event $event) {
        if ($event->getData('user')['role'] === 'admin') {
            return ['plugin'=>null,'controller' => 'Pages', 'action' => 'admin'];
        }
        if ($event->getData('user')['role'] === 'user') {
            return ['plugin'=>null,'controller' => 'Pages', 'action' => 'home'];
        }

        // other roles will be redirected to the url configured at 'Auth.loginRedirect' in "src/config/users.php"
    }
);