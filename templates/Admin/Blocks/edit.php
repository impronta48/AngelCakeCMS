<?= $this->element('v-admin-navbar', ['event' => $block]); ?>
<div class="blocks form content">
  <?= $this->Form->create($block) ?>
  <fieldset>
    <legend><?= __('Edit Block') ?></legend>
    <?php
    echo $this->Form->control('title');
    ?>
    <textarea class="xeditor form-control" name="body" rows="15"><?= $block->body ?></textarea>
  </fieldset>
  <br>
  <?= $this->Form->button(__('Submit')) ?>
  <?= $this->Form->end() ?>
</div>