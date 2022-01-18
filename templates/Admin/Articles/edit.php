<?php

use Cake\Core\Configure;
echo $this->Html->script("jquery-1.10.2.min");
echo $this->Html->script("jquery-ui.min");

$this->assign('title', 'Article Edit: ' . $article->title); ?>
<?php $sitedir = Configure::read('sitedir'); ?>

<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $article]); ?>
  <br>

  <?= $this->Form->create($article, [
    'type' => 'file',
    'class' => 'form'
  ]); ?>

  <?php echo $this->Form->control('title'); ?>
  <?php
  echo $this->Form->control('slug');
  echo $this->Form->control('renewSlug', ['type' => 'checkbox']);
  ?>

  <?php $path = "/images{$article->copertina}?w=180&h=180&fit=crop"; ?>

  <b-card header="Copertina">
    <?= $this->element(
      'upload',
      [
        'model' => 'Articles',
        'field' => 'copertina',
        'multiple' => false,
        'temp' => $new ? true : false,
        'filetype' => 'image/*',
      ] + ($new ? [] : [
        'destination' => $article->destination ? $article->destination->slug : 'null',
        'files' => [ $article->copertina ],
        'id' => $article->id,
      ])
    ); ?>

    <div class="card-footer">
      <span class="small">Massima dimensione dell'immagine: <?= ini_get("upload_max_filesize") ?>B</span>
    </div>
  </b-card>

  <?php echo $this->Form->control('body', ['label' => 'Corpo Articolo', 'class' => 'editor']); ?>

  <?php echo $this->Form->control('destination_id', ['empty' => '---']);  ?>

  <b-card header="Immagini associate a questo articolo" header-bg-variant="info" header-text-variant="white">
    <?= $this->element(
      'upload',
      [
        'model' => 'Articles',
        'field' => 'galleria',
        'multiple' => true,
        'temp' => $new ? true : false,
        'filetype' => 'image/*',
      ] + ($new ? [] : [
        'destination' => $article->destination ? $article->destination->slug : 'null',
        'files' => $article->galleria,
        'id' => $article->id,
      ])
    ); ?>

    <div class="card-footer">
      <span class="small">Massima dimensione dell'allegato: <?= ini_get("upload_max_filesize") ?>B</span>
    </div>
  </b-card>

  <b-card header="File allegati a questo articolo" header-bg-variant="info" header-text-variant="white">
    <?= $this->element(
      'upload',
      [
        'model' => 'Articles',
        'field' => 'allegati',
        'multiple' => true,
        'temp' => $new ? true : false,
        'filetype' => '.pdf,.doc,.xls,.ppt,.odt,.docx,.odp,.kml',
      ] + ($new ? [] : [
        'destination' => $article->destination ? $article->destination->slug : 'null',
        'files' => $article->allegati,
        'id' => $article->id,
      ])
    ); ?>

    <div class="card-footer">
      <span class="small">Massima dimensione dell'allegato: <?= ini_get("upload_max_filesize") ?>B</span>
      <span class="small">Ammessi file pdf|doc|xls|ppt|odt|docx|odp|kml</span>
    </div>
  </b-card>

  <?= $this->Form->control('published', ['label' => 'Pubblicato']); ?>
  <?= $this->Form->control('archived', ['label' => 'Archiviato']); ?>
  <?= $this->Form->control('promoted', ['label' => 'Promosso in Home Page']); ?>
  <?= $this->Form->control('slider', ['label' => 'Visibile nello Slider']); ?>

  <div class="card text-white bg-secondary mt-3">
    <div class="card-body">
      <h5 class="card-title">SEO</h5>
      <?= $this->Form->control('keywords', ['label' => 'KeyWords']); ?>
      <?= $this->Form->control('description', ['label' => 'Description']); ?>
      <?= $this->Form->control('url_canonical', ['label' => 'Canonical URL']); ?>
    </div>
  </div>

  <div class="card text-white bg-secondary mt-3 mb-2">
    <div class="card-body">
      <h5 class="card-title">Pubblicazione</h5>
      <?= $this->Form->control('modified', ['label' => 'Ultima Modifica', 'type' => 'datetime']); ?>
      <?= $this->Form->control('user_id'); ?>
    </div>
  </div>

  <?= $this->Form->hidden('id'); ?>
  <?= $this->Form->button(__("Save")); ?>
  <?= $this->Form->end() ?>
</div>