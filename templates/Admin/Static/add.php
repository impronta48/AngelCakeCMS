<?php

use Cake\Routing\Router;

$basePath = Router::url(['controller' => 'static', 'action' => 'index']) . '/index';
$prevPath = $basePath;
$this->assign('title', "Static File Creator");
?>

<h1>Nuovo file statico</h1>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= $basePath ?>">Home</a></li>
    <?php foreach ($path as $p) : ?>
      <li class="breadcrumb-item" aria-current="page">
        <?php $prevPath .= "/$p"; ?>
        <a href="<?= $prevPath ?>">
          <?= $p ?>
        </a>
      </li>
    <?php endforeach ?>
    <li class="breadcrumb-item last" aria-current="page">
      Nuovo File
    </li>
  </ol>
</nav>
<?= $this->Form->create() ?>
<?= $this->Form->control('fname', ['label' => 'Nome del file', 'value' => 'nome-file.md']) ?>
<i>Il file deve finire con l'estensione md.<br> I file che iniziano con _ sono considerati bozze (non visibili sul sito).</i>
<?= $this->Form->control('static', ['type' => 'textarea', 'rows' => 20, 'label' => 'Markdown con FrontMatter', 'value' => $template]) ?>
<?= $this->Form->button('Salva') ?>
<?= $this->Form->end() ?>