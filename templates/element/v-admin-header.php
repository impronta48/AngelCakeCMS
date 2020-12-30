<?php

use Cake\Routing\Router;
?>
<?php
//Carico le voci di menu extra portate da ogni plugin nel proprio element
$this->element('v-admin-extra-main-menu'); ?>
<div>
  <b-navbar toggleable="lg" type="dark" variant="info">
    <b-navbar-brand href="<?= Router::url(Router::url(['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Pages', 'action' => 'display', 'admin'])) ?>">AngelCake CMS</b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav>
        <b-nav-item href="<?= Router::url(['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Articles', 'action' => 'index']) ?>">Articoli</b-nav-item>
        <?= $this->fetch('extra-main-menu') ?>
        <b-nav-item href="/users/index">Utenti</b-nav-item>
        <b-nav-item href="/admin/blocks/index">Blocchi</b-nav-item>
      </b-navbar-nav>

      <!-- Right aligned nav items -->
      <b-navbar-nav class="ml-auto">
        <b-nav-item href="/" target="preview">Vai al sito</b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</div>