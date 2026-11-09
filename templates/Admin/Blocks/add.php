<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Block $block
 */
$this->assign('vue', 'mix');
?>
<?= $this->element('v-admin-navbar', ['event' => $block]); ?>

<div class="blocks form content">
  <?= $this->Form->create($block, ['type' => 'file']) ?>
  <fieldset>
    <legend><?= __('Add Block') ?></legend>
    <?php
    echo $this->Form->control('title');
    ?>
    <textarea class="xeditor form-control" name="body" rows="15"><?= $block->body ?></textarea>
  </fieldset>
  <br>

  <b-card header="File allegati a questo blocco" header-bg-variant="info" header-text-variant="white">
    <?= $this->element(
      'upload',
      [
        'model' => 'Blocks',
        'field' => 'allegati',
        'multiple' => true,
        'temp' => true,
        'filetype' => '.jpg,.jpeg,.png,.gif,.webp'
      ]
    ); ?>

    <div class="card-footer">
      <span class="small">Massima dimensione dell'allegato: <?= ini_get("upload_max_filesize") ?>B</span>
      <span class="small">Ammessi file jpg|jpeg|png|gif|webp</span>
    </div>
  </b-card>

  <?= $this->Form->button(__('Submit')) ?>
  <?= $this->Form->end() ?>
</div>