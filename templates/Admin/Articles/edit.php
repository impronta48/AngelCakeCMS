<?php

use Cake\Core\Configure;

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
    <b-row>
      <b-col>
        <b-img thumbnail fluid src="<?= $path ?>"></b-img>
      </b-col>
    </b-row>
    <template #footer>
      <b-row>

        <?php if (isset($article->copertina)) : ?>
          <b-col cols="9">
            <b-form-file placeholder="Scegli un'immagine oppure trascinala qui..." drop-placeholder="Trascina qui un'immagine" id="newcopertina" name="newcopertina" accept="image/*"></b-form-file>
            <span class="small">Massima dimensione dell'immagine: <?= ini_get("upload_max_filesize") ?>B</span>
          </b-col>
          <b-col cols="3">
            <a href="<?= Cake\Routing\Router::url(['controller' => 'Articles', 'action' => 'remove_file', '?' => ['fname' => $article->copertina]]) ?>" title="Elimina" class="btn btn-danger btn-sm">
              Elimina Copertina
            </a>
          </b-col>
        <?php endif; ?>
      </b-row>
    </template>
  </b-card>
  <br>

  <?php echo $this->Form->control('body', ['label' => 'Corpo Articolo', 'class' => 'editor']); ?>

  <?php echo $this->Form->control('destination_id', ['empty' => '---']);  ?>

  <div class="card card-info">
    <div class="card-body">
      <h5 class="card-title"><i class="bi bi-image"></i> Immagini associate a questo articolo</h5>
      <?php if (0 && isset($article->gallery)) : ?>
        <?php echo $this->element('img-gallery-vue', [
          'images' => $article->gallery,
          'id' => $article->id,
          'canEdit' => true,
          'canDelete' => true,
          'small_size' => [180, 180],
          'large_size' => [-1, -1]
        ]); ?>
      <?php endif; ?>
      <?php echo $this->Form->file('newgallery. ', [
        'multiple' => 'multiple',
        'label' => 'Immagini dell\'articolo',
        'after' => 'In questo campo puoi caricare più immagini, semplicemente selezionandone di più',
      ]); ?>
    </div>
    <div class="card-footer">
      <span class="small">Massima dimensione dell'allegato: <?= ini_get("upload_max_filesize") ?>B</span>
    </div>
  </div>

  <div class="card card-info">

    <div class="card-body">
      <h5 class="card-title"><i class="bi bi-paperclip"></i> File allegati a questo articolo</h5>

      <?php if (isset($article->allegati)) : ?>
        <ul>
          <?php foreach ($article->allegati as $file) : ?>
            <li>
              <a href="<?= $file ?>"><i class="bi bi-file-o"></i> <?= basename($file) ?></a>
              <a href="<?= Cake\Routing\Router::url(['controller' => 'Articles', 'action' => 'remove_file', '?' => ['fname' => $file]]) ?>" title="Elimina" class="btn btn-danger btn-xs">
                Elimina
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif ?>
      <?php echo $this->Form->file('newallegati. ', [
        'multiple' => 'multiple',
        'label' => 'Allegati dell\'articolo',
        'after' => 'In questo campo puoi caricare più file, semplicemente selezionandone di più',
      ]); ?>
    </div>
    <div class="card-footer">
      <span class="small">Massima dimensione dell'allegato: <?= ini_get("upload_max_filesize") ?>B</span>
    </div>
  </div>
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