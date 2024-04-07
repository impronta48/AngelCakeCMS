<?php $this->assign('title', 'Aggiungi Articolo'); ?>
<?php $this->assign('vue', 'mix'); // Needed because this page is also rendered by `add` 
?>
<?php $new = !isset($article->id); ?>

<div class="container" id="article">
  <?= $this->element('v-admin-navbar', ['event' => $article]); ?>
  <br>

  <?= $this->Form->create($article, ['type' => 'file',  'class' => 'form']); ?>
  <fieldset>
    <?php echo $this->Form->control('title'); ?>

    <div class="card card-info">

      <div class="card-body">
        <h3 class="card-title"><i class="bi bi-image"></i> Immagine di copertina</h3>
        <?= $this->element(
          'upload',
          [
            'model' => 'Articles',
            'field' => 'newcopertina',
            'multiple' => false,
            'temp' => $new ? true : false,
            'filetype' => 'image/*',
          ] + ($new ? [] : [
            'destination' => $article->destination ? $article->destination->slug : 'null',
            'files' => [$article->copertina],
            'id' => $article->id,
          ])
        ); ?>
        <?= $this->element('copertina_bkg_pos', [
          'entity' => $article
        ]); ?>
      </div>
      <div class="card-footer">
        <span class="small">Massima dimensione dell'immagine: <?= ini_get("upload_max_filesize") ?>B</span>
      </div>
    </div>
    <textarea class="editor" name="body"><?= $article->body ?></textarea>
    <?php
    echo $this->Form->control('destination_id', ['empty' => '---']);
    ?>
  </fieldset>

  <div class="card card-info">
    <div class="card-body">
      <h3 class="card-title"><i class="bi bi-image"></i> Immagini associate a questo articolo</h3>
      <?php echo $this->Form->file('newgallery. ', [
        'multiple' => 'multiple',
        'label' => 'Immagini del articolo',
        'after' => 'In questo campo puoi caricare più immagini, semplicemente selezionandone di più',
      ]); ?>
    </div>
    <div class="card-footer">
      <span class="small">Massima dimensione dell'immagine: <?= ini_get("upload_max_filesize") ?>B</span>
    </div>
  </div>

  <div class="card card-info">



    <div class="card-body">
      <h3 class="card-title"><i class="bi bi-paperclip"></i> File allegati a questo articolo</h3>
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
  <?= $this->Form->control('modified', ['label' => 'Ultima Modifica', 'type' => 'text', 'format' => 'Y-m-d']); ?>
  <?= $this->Form->control('user_id', ['value' => $user]); ?>
  <?= $this->Form->button(__("Save")); ?>
  <?= $this->Form->end() ?>

</div>