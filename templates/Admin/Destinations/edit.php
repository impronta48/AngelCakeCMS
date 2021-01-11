<?php $this->assign('title', 'Destination Edit: ' . $destination->title); ?>


<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $destination]); ?>



  <?= $this->Form->create($destination); ?>
  <fieldset>

    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('slug');
    echo $this->Form->control('show_in_list');
    ?>
  </fieldset>
  <?= $this->Form->button(__("Save")); ?>
  <?= $this->Form->end() ?>
</div>