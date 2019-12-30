<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 */
?>
<?php
$this->extend('../Layout/TwitterBootstrap/dashboard');

$this->start('tb_actions');
?>
    <li><?= $this->Html->link(__('List Participants'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add']) ?> </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?= $this->Html->link(__('List Participants'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add']) ?> </li>
</ul>
<?php
$this->end();
?>
<?= $this->Form->create($participant); ?>
<fieldset>
    <legend><?= __('Add {0}', ['Participant']) ?></legend>
    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('surname');
    echo $this->Form->control('email');
    echo $this->Form->control('tel');
    echo $this->Form->control('privacy');
    echo $this->Form->control('dob',['label'=>'Data di nascita',['type'=>'date','dateFormat'=>'DMY','maxYear'=>2005,'minYear'=>1993]]);
    echo $this->Form->control('diet');
    echo $this->Form->control('note');
    echo $this->Form->control('event_id', ['options' => $events]);
    ?>
</fieldset>
<?= $this->Form->button(__("Add")); ?>
<?= $this->Form->end() ?>
