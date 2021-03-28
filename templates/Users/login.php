<?php

use Cake\Core\Configure;
?>
<div class="col-md-4 offset-md-4">
  <div class="card">
    <img class="card-img-top" src="<?= Configure::read('MailLogo'); ?>" alt="Accedi a Cyclomap">
    <div class="card-body">
      Benvenuto in <b>Angelcake</b>, il sistema di gestione dei contenuti di iMpronta.<br>
      Per accedere a questa pagina è necessario essere autorizzati con il proprio account Google.
    </div>
    <div class="card-footer">
      <?= $this->Form->postLink(
        '<img src="https://img.icons8.com/color/16/000000/google-logo.png">  Accedi con Google',
        [
          'prefix' => false,
          'plugin' => 'ADmad/SocialAuth',
          'controller' => 'Auth',
          'action' => 'login',
          'provider' => 'google',
          '?' => ['redirect' => $this->request->getQuery('redirect')]
        ],
        [
          'escape' => false,
          'class' => 'btn btn-lg btn-block text-uppercase btn-outline-primary'
        ]
      ); ?>
    </div>

  </div>

</div>