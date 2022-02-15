<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
?>
<?php
//Carico le voci di menu extra portate da ogni plugin nel proprio element
$this->element('v-admin-extra-main-menu'); ?>
<div>
  <b-navbar toggleable="lg" type="dark" variant="info">
    <b-navbar-brand href="<?= Router::url(Router::url(['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Pages', 'action' => 'display', 'admin'])) ?>"><?= Configure::read('sitename') ?></b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav>
        <b-nav-item href="<?= Router::url(['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Articles', 'action' => 'index']) ?>">
          <b-icon-journal-text></b-icon-journal-text>
          <?= __('Articoli') ?>
        </b-nav-item>
        <?= $this->fetch('extra-main-menu') ?>
        <b-nav-item href="/admin/static/index">
          <b-icon-markdown></b-icon-markdown>
          <?= __('File Statici') ?>
        </b-nav-item>
        <b-nav-item href="/admin/blocks/index">
          <b-icon-bounding-box></b-icon-bounding-box>
          <?= __('Blocchi') ?>
        </b-nav-item>
        <b-nav-item href="/admin/users">
          <b-icon-people></b-icon-people>
          <?= __('Utenti') ?>
        </b-nav-item>
      </b-navbar-nav>

      <!-- Right aligned nav items -->
      <b-navbar-nav class="ml-auto">
        <?php
          $arr = $this->request->getAttribute('params');
          unset($arr['lang']);
        ?>
        <?php foreach (Configure::read('I18n.languages') as $lang) : ?>
          <?php $route = $lang != Configure::read('App.language') ? array_merge(['lang' => $lang], $arr) : $arr ; ?>
          <b-nav-item href="<?= Router::reverse($route) ?>">
            <?php $path = substr($lang, 0, 2) ?>
            <?= $this->Html->image("flags/{$path}.png") ?>
          </b-nav-item>
        <?php endforeach; ?>
        <b-nav-item href="/" target="preview">

          <?= __('Anteprima sito') ?>
          <b-icon-globe></b-icon-globe>
        </b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</div>