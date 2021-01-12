<?php

use Cake\Routing\Router;

$icons['md'] = "fa-file-code-o";
$icons['jpg'] = "fa-file-image-o";
$icons['png'] = "fa-file-image-o";
$pathStr = implode('/', $path);
$basePath = Router::url(['controller' => 'static', 'action' => 'index']) . '/index';
$prevPath = $basePath;
$this->assign('title', "$pathStr - Static File Manager");
?>
<h1>File Statici</h1>

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
  </ol>
</nav>

<b-dropdown id="dropdown-1" text="Nuovo" class="float-right">
  <b-dropdown-item href="#"><i class="fa fa-folder"></i> Nuova Cartella</b-dropdown-item>
  <b-dropdown-item href="#"><i class="fa fa-file-o"></i> Nuovo Allegato</b-dropdown-item>
  <b-dropdown-item href="#"><i class="fa fa-file-image-o"></i> Nuova Immagine</b-dropdown-item>
  <b-dropdown-item href="<?= Router::url(array_merge(['action' => 'add'], $path)) ?>"><i class="fa fa-file-text"></i> Nuovo MarkDown</b-dropdown-item>
</b-dropdown>

<br>

<table class="table table-striped">
  <thead>
    <th>Nome</th>
    <th>Ext</th>
    <th>Azioni</th>
  </thead>
  <?php foreach ($files[0] as $f) : ?>
    <tr>
      <td>
        <a href="<?= "$basePath/$pathStr/$f" ?>">
          <i class="fa fa-folder"></i> <?= $f ?>
        </a>
      </td>
      <td>DIR</td>
      <td>
        <a href="#" class="btn btn-sm " title="Rinomina"><i class="fa fa-i-cursor"></i> </a>
        <a href="#" class="btn btn-sm " title="Elimina"><i class="fa fa-trash"></i> </a>
      </td>
    </tr>
  <?php endforeach ?>
  <?php foreach ($files[1] as $f) : ?>
    <?php if ($f[0] != '.') : ?>
      <tr>
        <?php $pathparts = pathinfo($f); ?>
        <td>
          <?php if (isset($icons[$pathparts['extension']])) : ?>
            <i class="fa <?= $icons[$pathparts['extension']] ?>"></i>
          <?php else : ?>
            <i class="fa fa-file-o"></i>
          <?php endif ?>

          <?= $pathparts['basename'] ?>
        </td>
        <td><?= $pathparts['extension'] ?></td>
        <td>
          <a href="<?= Router::url(array_merge(['action' => 'edit'], $path, [$f]))  ?>" class="btn btn-sm " title="Modifica"><i class="fa fa-pencil"></i> </a>
          <a href="#" class="btn btn-sm " title="Rinomina"><i class="fa fa-i-cursor"></i> </a>
          <?= $this->Form->postLink('', array_merge(['action' => 'delete'], $path, [$f]), ['confirm' => __('Are you sure you want to delete # {0}?', $f), 'title' => __('Delete'), 'class' => 'btn btn-default fa fa-trash']) ?>
        </td>
      </tr>
    <?php endif ?>
  <?php endforeach ?>
</table>