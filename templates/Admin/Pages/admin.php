<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->element('v-admin-extra-main-icons'); //Carico le icone extra fornite dal template
?>

<div class="container mt-5">

  <h2 style="text-align:center">Benvenuto nel sistema di gestione dei contenuti<br> <?= Configure::read('sitename') ?></h2>

  <div class="row">
    <div class="col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Articles', 'action' => 'index']) ?>"><img src="/img/admin/articoli.png" class="img-responsive icona"></a>

      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Articles', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Articoli</h4>
      </a>

    </div>

    <div class=" col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Destinations', 'action' => 'index']) ?>"><img src="/img/admin/categorie.png" class="img-responsive icona"></a>
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Destinations', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Destinazioni o Categorie</h4>
      </a>

    </div>

    <div class=" col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'index']) ?>"><img src="/img/admin/users.png" class="img-responsive icona"></a>
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Utenti</h4>
      </a>

    </div>

    <?= $this->fetch('extra-main-icons'); //Carico le icone extra fornite dai plugin nell'element extra-main-icons 
    ?>

    <div class=" col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Events', 'action' => 'index']) ?>"><img src="/img/admin/eventi.png" class="img-responsive icona"></a>
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Events', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Eventi</h4>
      </a>

    </div>

    <div class=" col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Participants', 'action' => 'index']) ?>"><img src="/img/admin/partecipanti.png" class="img-responsive icona"></a>
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Participants', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Partecipanti</h4>
      </a>

    </div>

    <div class=" col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Blocks', 'action' => 'index']) ?>"><img src="/img/admin/chain.svg" class="img-responsive icona"></a>
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Blocks', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Blocchi</h4>
      </a>
    </div>

    <div class=" col-md-2 text-center mt-5">
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Tags', 'action' => 'index']) ?>"><img src="/img/admin/tags.png" class="img-responsive icona"></a>
      <a href="<?= Router::url(['prefix' => 'Admin', 'controller' => 'Tags', 'action' => 'index']) ?>" class="titoloAdmin">
        <h4>Tags</h4>
      </a>
    </div>

    
  </div>

  <hr>
  <?php if ($this->Identity->isLoggedIn()) : ?>
    <div class="row">
      <div class="col text-center mt-5">
        <div class="divLogout">
          <a type="button" class="btnLogout btn btn-danger" href="/logout">LOGOUT</a>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>