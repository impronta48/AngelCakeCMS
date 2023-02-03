<?php

use Cake\Routing\Router;

$basePath = Router::url(['controller' => 'static', 'action' => 'index']) . '/index';
$prevPath = $basePath;
$this->assign('title', "$title - Static File Editor");
?>

<h1>Modifica file statico</h1>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= $basePath ?>">Home</a></li>
    <?php $last = array_pop($path); ?>
    <?php foreach ($path as $p) : ?>
      <li class="breadcrumb-item" aria-current="page">
        <?php $prevPath .= "/$p"; ?>
        <a href="<?= $prevPath ?>">
          <?= $p ?>
        </a>
      </li>
    <?php endforeach ?>
    <li class="breadcrumb-item last" aria-current="page">
      <?= $last ?>
    </li>
  </ol>
</nav>
<?= $this->Form->create() ?>
<?= $this->Form->control('static', ['type' => 'textarea', 'rows' => 20, 'value' => $static, 'label' => 'Markdown con FrontMatter']) ?>
<?= $this->Form->button('Salva') ?>
<?= $this->Form->end() ?>