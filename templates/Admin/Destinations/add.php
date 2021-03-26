<?php $this->assign('title', 'Destination ADD'); ?>


<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $destination]); ?>


  <?= $this->Form->create($destination); ?>
  <fieldset>
    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('slug');
    echo $this->Form->control('nazione_id', ['options' => [0 => 'it', 1=> 'fr']]);
    echo $this->Form->control('nomiseo');
    echo $this->Form->control('show_in_list');
    ?>
  </fieldset>
  <?= $this->Form->button(__("Add")); ?>
  <?= $this->Form->end() ?>
</div>