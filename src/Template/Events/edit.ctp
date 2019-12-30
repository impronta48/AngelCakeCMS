<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 */
?>
<?php
$this->extend('../Layout/TwitterBootstrap/dashboard');

$this->start('tb_actions');
?>
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $event->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]
    )
    ?>
    </li>
    <li><?= $this->Html->link(__('List Events'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Destinations'), ['controller' => 'Destinations', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Destination'), ['controller' => 'Destinations', 'action' => 'add']) ?> </li>
    <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    <li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add']) ?> </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $event->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]
    )
    ?>
    </li>
    <li><?= $this->Html->link(__('List Events'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Destinations'), ['controller' => 'Destinations', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Destination'), ['controller' => 'Destinations', 'action' => 'add']) ?> </li>
    <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    <li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add']) ?> </li>
</ul>
<?php
$this->end();
?>
<?= $this->Form->create($event); ?>
<fieldset>
    <legend><?= __('Edit {0}', ['Event']) ?></legend>
    <?php
    echo $this->Form->control('title');
    echo $this->Form->control('description');
    echo $this->Form->control('max_pax');
    echo $this->Form->control('place');
    echo $this->Form->control('destination_id', ['options' => $destinations]);
    echo $this->Form->control('start_time');
    echo $this->Form->control('end_time');
    echo $this->Form->control('min_year');
    echo $this->Form->control('max_year');
    echo $this->Form->control('user_id', ['options' => $users]);
    ?>
</fieldset>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
