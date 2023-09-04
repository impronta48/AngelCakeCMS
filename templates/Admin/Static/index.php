<?php

use Cake\Routing\Router;

$icons['md'] = "bi-markdown";
$icons['jpg'] = "bi-file-earmark-lock-fill";
$icons['png'] = "bi-file-earmark-lock-fill";
$pathStr = implode('/', $path);
$basePath = Router::url(['controller' => 'static', 'action' => 'index']) . '/index';
$prevPath = $basePath;
$this->assign('title', "$pathStr - Static File Manager");
?>
<h1>File Statici</h1>
<a href="<?= Router::url(['action'=> 'getWebdav']) ?>" class="btn btn-primary float-right m-1"><i class="bi bi-cloud"></i> Importa aggioramenti da NextCloud</a>

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
  <b-dropdown-item href="#"><i class="bi bi-folder"></i> Nuova Cartella</b-dropdown-item>
  <b-dropdown-item href="#"><i class="bi bi-file-o"></i> Nuovo Allegato</b-dropdown-item>
  <b-dropdown-item href="#"><i class="bi bi-file-image-o"></i> Nuova Immagine</b-dropdown-item>
  <b-dropdown-item href="<?= Router::url(array_merge(['action' => 'add'], $path)) ?>"><i class="bi bi-file-text"></i> Nuovo MarkDown</b-dropdown-item>
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
          <i class="bi bi-folder"></i> <?= $f ?>
        </a>
      </td>
      <td>DIR</td>
      <td>
        <a href="#" class="btn btn-sm " title="Rinomina"><i class="bi bi-i-cursor"></i> </a>
        <a href="#" class="btn btn-sm " title="Elimina"><i class="bi bi-trash"></i> </a>
      </td>
    </tr>
  <?php endforeach ?>
  <?php foreach ($files[1] as $f) : ?>
    <?php if ($f[0] != '.') : ?>
      <tr>
        <?php $pathparts = pathinfo($f); ?>
        <td>
          <?php if (isset($icons[$pathparts['extension']])) : ?>
            <i class="bi <?= $icons[$pathparts['extension']] ?>"></i>
          <?php else : ?>
            <i class="bi bi-file-o"></i>
          <?php endif ?>

          <?= $pathparts['basename'] ?>
        </td>
        <td><?= $pathparts['extension'] ?></td>
        <td>
          <a href="<?= Router::url(array_merge(['action' => 'edit'], $path, [$f]))  ?>" class="btn btn-sm " title="Modifica"><i class="bi bi-pencil"></i> </a>
          <a href="#" class="btn btn-sm " title="Rinomina"><i class="bi bi-cursor-text"></i> </a>
          <?= $this->Form->postLink('', array_merge(['action' => 'delete'], $path, [$f]), ['confirm' => __('Are you sure you want to delete # {0}?', $f), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
        </td>
      </tr>
    <?php endif ?>
  <?php endforeach ?>
</table>