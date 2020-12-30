<?= $this->element('v-admin-navbar', ['event' => $block]); ?>
<div class="blocks form content">
  <?= $this->Form->create($block) ?>
  <fieldset>
    <legend><?= __('Edit Block') ?></legend>
    <?php
    echo $this->Form->control('title');
    ?>
    <textarea class="editor" name="body"><?= $block->body ?></textarea>
  </fieldset>
  <br>
  <?= $this->Form->button(__('Submit')) ?>
  <?= $this->Form->end() ?>
</div>