<?php

use App\Event\SocialAuthListener;
use Cake\Event\EventManager;

$SocialAuthListener = new SocialAuthListener();
EventManager::instance()->on($SocialAuthListener);
