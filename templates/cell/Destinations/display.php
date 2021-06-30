<?php

use Cake\Routing\Router;

?>

<li class="dropdown"><a href="<?= Router::url(['controller' => 'destinations', 'action' => 'index', 'plugin' => false]) ?>">Attività</a>
  <ul>
    <?php foreach ($destinations as $d): ?>
      <li><a href="<?= Router::url(['controller' => 'destinations', 'action' => 'view', $d->slug, 'plugin'=> false]) ?>"><?= $d->name ?></a></li>
    <?php endforeach; ?>
  </ul>
</li>