<?php $this->assign('title', 'Event Edit: ' . $event->title); ?>



<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $event]); ?>

  <?= $this->Form->create($event); ?>
  <fieldset>
    <?php
    echo $this->Form->control('title');
    echo $this->Form->control('description', ['id' => 'editor']);
    echo $this->Form->control('max_pax');
    echo $this->Form->control('place');
    echo $this->Form->control('destination_id', ['options' => $destinations]);
    echo $this->Form->control('start_time');
    echo $this->Form->control('end_time');
    echo $this->Form->control('min_year');
    echo $this->Form->control('max_year');
    echo $this->Form->control('slug');
    echo $this->Form->control('user_id');
    ?>
  </fieldset>
  <?= $this->Form->button(__("Save")); ?>
  <?= $this->Form->end() ?>
</div>