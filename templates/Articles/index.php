<?php

use Cake\Routing\Router;

?>
<div class="container">
  <h4>I nostri aggiornamenti</h4>
  <div class="card-deck">

    <?php foreach ($articles as $a) : ?>
      <?php $dname = (!empty($a->destination)) ? $a->destination->name : ''; ?>
      <div class="card" style="max-width: 18rem;">
        <a href="<?= Router::url(['action' => 'view', $a->slug]) ?>">
        <img class="card-img-top" src="/images<?= $a->copertina ?>?w=309&h=200&fit=crop&fm=webp" alt="<?= $dname  . ", " . $a->title ?>">
        </a>
        <div class="card-body">
          <h5 class="card-title">
            <a href="<?= Router::url(['action' => 'view', $a->slug]) ?>">
              <?= $a->title ?>
            </a>
            </h5>
          <p class="card-text"><?= strip_tags($this->Text->truncate($a->body, 200)); ?></p>
        </div>
      </div>


    <?php endforeach ?>
  </div>
</div>