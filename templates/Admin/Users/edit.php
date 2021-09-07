<?php

use Cake\Core\Configure;
?>
<h1>Aggiungi utente</h1>

<?= $this->Form->create($user) ?>
<?= $this->Form->hidden('id'); ?>
<?= $this->Form->control('gmail'); ?>
<?= $this->Form->control('group_id', ['options' => Configure::read('groups')]); ?>
<?= $this->Form->control('destination_id', ['empty' => '--- Tutte ---']); ?>
<?= $this->Form->button('Aggiungi'); ?>

<?= $this->Form->end(); ?>