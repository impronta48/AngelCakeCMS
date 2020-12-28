<?php $this->assign('title', 'Article Add'); ?>

<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $article]); ?>
  <br>

  <?= $this->Form->create($article, ['type' => 'file']); ?>
  <fieldset>
    <?php echo $this->Form->control('title'); ?>

    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-image"></i> Immagine di copertina</h3>
      </div>
      <div class="panel-body">
        <?php echo $this->Form->file('newcopertina', [
          'label' => 'Immagine di copertina',
          'after' => 'In questo campo puoi caricare una sola immagine',
        ]); ?>
      </div>
    </div>

    <?php
    echo $this->Form->control('body', ['class' => 'jquery']);
    echo $this->Form->control('destination_id', ['empty' => '---']);
    ?>
  </fieldset>

  <div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> Immagini associate a questo articolo</h3>
    </div>
    <div class="panel-body">
      <?php echo $this->Form->file('newgallery. ', [
        'multiple' => 'multiple',
        'label' => 'Immagini del articolo',
        'after' => 'In questo campo puoi caricare più immagini, semplicemente selezionandone di più',
      ]); ?>
    </div>
  </div>

  <div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> File allegati a questo articolo</h3>
    </div>
    <div class="panel-body">
      <?php echo $this->Form->file('newallegati. ', [
        'multiple' => 'multiple',
        'label' => 'Allegati dell\'articolo',
        'after' => 'In questo campo puoi caricare più file, semplicemente selezionandone di più',
      ]); ?>
    </div>
  </div>
  <?= $this->Form->control('published', ['label' => 'Pubblicato']); ?>
  <?= $this->Form->control('archived', ['label' => 'Archiviato']); ?>
  <?= $this->Form->control('promoted', ['label' => 'Promosso in Home Page']); ?>
  <?= $this->Form->control('slider', ['label' => 'Visibile nello Slider']); ?>
  <?= $this->Form->control('modified', ['label' => 'Ultima Modifica', 'type' => 'text', 'format' => 'Y-m-d']); ?>
  <?= $this->Form->control('user_id'); ?>
  <?= $this->Form->button(__("Save")); ?>
  <?= $this->Form->end() ?>

</div>