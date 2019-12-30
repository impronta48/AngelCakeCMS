<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Destination $destination
 */
?>
<?php
$this->extend('../Layout/TwitterBootstrap/dashboard');

$this->start('tb_actions');
?>
    <li><?= $this->Html->link(__('List Destinations'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add']) ?> </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?= $this->Html->link(__('List Destinations'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add']) ?> </li>
</ul>
<?php
$this->end();
?>
<?= $this->Form->create($destination); ?>
<fieldset>
    <legend><?= __('Add {0}', ['Destination']) ?></legend>
    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('slug');
    echo $this->Form->control('show_in_list');
    echo $this->Form->control('facebook');
    echo $this->Form->control('instagram');
    echo $this->Form->control('lc');
    echo $this->Form->control('ef');
    echo $this->Form->control('anno_attivazione');
    echo $this->Form->control('comuni');
    echo $this->Form->control('tipologia');
    echo $this->Form->control('presidente');
    echo $this->Form->control('coach');
    echo $this->Form->control('fondazione_locale');
    echo $this->Form->control('descrizione',['class'=>'jquery-ckeditor']);
    echo $this->Form->control('email');
    echo $this->Form->control('chiuso');
    ?>
</fieldset>
<?= $this->Form->button(__("Add")); ?>
<?= $this->Form->end() ?>
