<?php

use Cake\Core\Configure;

$rs = Configure::read('rclone.remoteServer');
?>
<div class="container">
  <h2><?= $msg ?></h2>
  <hr>
  <p>Con questa funzionalità puoi importare le pagine statiche (file in formato markdown .md) da NextCloud/Owncloud e convertirle in pagine web per il tuo sito</p>

  <h3>Ecco la procedura se hai installato il client NextCloud</h3>
  <ul>
    <li>Accedi alla cartella NextCloud sul tuo computer.</li>
    <li>Accedi alla cartella <b><?= Configure::read('rclone.staticFolder') ?></b></li>
    <li>Modifica i file md con un editor esterno - (consiglio <a href="https://marktext.app/">https://marktext.app/</a>)</li>
    <li>Premi il pulsante importa e attendi l'importazione.</li>
  </ul>


  <h3>Ecco la procedura se usi NextCloud Web</h3>
  <ul>
    <li>Collegati a NextCloud - <?= $this->Html->link($rs, $rs) ?></li>
    <li>Accedi alla cartella <b><?= Configure::read('rclone.staticFolder') ?></b>
      usando il tuo utente oppure l'utente <b><?= Configure::read('rclone.remoteUser') ?></b></li>
    <li>Modifica i file md con un editor esterno (quello nativo di nextcloud toglie il frontmatter, quindi non va bene)</li>
    <li>Ricarica i file che hai modificato su NextCloud</li>
    <li>Premi il pulsante importa e attendi l'importazione.</li>
  </ul>

  <?= $this->Form->create(); ?>
  <?= $this->Form->button(__("Importa Pagine Statiche da NextCloud")); ?>
  <?= $this->Form->end() ?>
</div>