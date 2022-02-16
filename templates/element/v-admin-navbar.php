<?php

use \Cake\Routing\Router;
use \Cake\Core\Configure;
use \Cake\I18n\I18n;

$contr = strtolower($this->request->getParam('controller'));
?>

<div class="v-admin-navbar">
  <b-nav tabs>
    <?php if (isset($event->id)) : ?>
      <b-nav-item active href="<?= Router::url(['prefix' => 'Admin', 'controller' => $contr, 'action' => 'edit', $event->id]) ?>">
        Edit
      </b-nav-item>
      <b-nav-item>
        <?= $this->Form->postLink(__('Delete'), ['prefix' => 'Admin', 'controller' => $contr, 'action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?>
      </b-nav-item>
      <b-nav-item href="<?= Router::url(['prefix' => false, 'action' => 'view', $event->id, 'target' => 'preview']) ?>">
        View
      </b-nav-item>
    <?php else : ?>
      <b-nav-item active href="<?= Router::url(['prefix' => 'Admin', 'controller' => $contr, 'action' => 'add']) ?>">
        Add
      </b-nav-item>
    <?php endif ?>

    <b-nav-item href="<?= Router::url(['prefix' => 'Admin', 'action' => 'index']) ?>">
      List
    </b-nav-item>

    <b-nav-item disabled class="ml-auto"></b-nav-item>

    <?php
      $arr = $this->request->getAttribute('params');
      unset($arr['lang']);
    ?>
    <?php foreach (Configure::read('I18n.languages') as $lang) : ?>
      <?php $route = $lang != Configure::read('App.language') ? array_merge(['lang' => $lang], $arr) : $arr ; ?>
      <b-nav-item <?= $lang == I18n::getLocale() ? 'active' : '' ?> href="<?= Router::reverse($route) ?>">
        <?php $path = substr($lang, 0, 2) ?>
        <?= $this->Html->image("flags/{$path}.png") ?>
      </b-nav-item>
    <?php endforeach; ?>
  </b-nav>
</div>