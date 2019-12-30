<?php
$this->extend('../Layout/TwitterBootstrap/dashboard');


$this->start('tb_actions');
?>
<li><?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id]) ?> </li>
<li><?= $this->Form->postLink(__('Delete Event'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?> </li>
<li><?= $this->Html->link(__('List Events'), ['action' => 'index']) ?> </li>
<li><?= $this->Html->link(__('New Event'), ['action' => 'add']) ?> </li>
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
<li><?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id]) ?> </li>
<li><?= $this->Form->postLink(__('Delete Event'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?> </li>
<li><?= $this->Html->link(__('List Events'), ['action' => 'index']) ?> </li>
<li><?= $this->Html->link(__('New Event'), ['action' => 'add']) ?> </li>
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
<div class="panel panel-default">
    <!-- Panel header -->
    <div class="panel-heading">
        <h3 class="panel-title"><?= h($event->title) ?></h3>
    </div>
    <table class="table table-striped" cellpadding="0" cellspacing="0">
        <tr>
            <td><?= __('Title') ?></td>
            <td><?= h($event->title) ?></td>
        </tr>
        <tr>
            <td><?= __('Place') ?></td>
            <td><?= h($event->place) ?></td>
        </tr>
        <tr>
            <td><?= __('Destination') ?></td>
            <td><?= $event->has('destination') ? $this->Html->link($event->destination->name, ['controller' => 'Destinations', 'action' => 'view', $event->destination->id]) : '' ?></td>
        </tr>
        <tr>
            <td><?= __('User') ?></td>
            <td><?= $event->has('user') ? $this->Html->link($event->user->email, ['controller' => 'Users', 'action' => 'view', $event->user->id]) : '' ?></td>
        </tr>
        <tr>
            <td><?= __('Id') ?></td>
            <td><?= $this->Number->format($event->id) ?></td>
        </tr>
        <tr>
            <td><?= __('Max Pax') ?></td>
            <td><?= $this->Number->format($event->max_pax) ?></td>
        </tr>
        <tr>
            <td><?= __('Min Year') ?></td>
            <td><?= $this->Number->format($event->min_year) ?></td>
        </tr>
        <tr>
            <td><?= __('Max Year') ?></td>
            <td><?= $this->Number->format($event->max_year) ?></td>
        </tr>
        <tr>
            <td><?= __('Start Time') ?></td>
            <td><?= h($event->start_time) ?></td>
        </tr>
        <tr>
            <td><?= __('End Time') ?></td>
            <td><?= h($event->end_time) ?></td>
        </tr>
        <tr>
            <td><?= __('Created') ?></td>
            <td><?= h($event->created) ?></td>
        </tr>
        <tr>
            <td><?= __('Modified') ?></td>
            <td><?= h($event->modified) ?></td>
        </tr>
        <tr>
            <td><?= __('Description') ?></td>
            <td><?= $this->Text->autoParagraph(h($event->description)); ?></td>
        </tr>
    </table>
</div>

<div class="panel panel-default">
    <!-- Panel header -->
    <div class="panel-heading">
        <h3 class="panel-title">Partecipanti a questo evento</h3>
        <?= $this->Html->link('Scarica XLS',['controller'=>'participants','action'=>'index','_ext'=>'xls',$event->id ]); ?>
    </div>
    <?php if (!empty($event->participants)): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Name') ?></th>
                <th><?= __('Surname') ?></th>
                <th><?= __('Email') ?></th>
                <th><?= __('Tel') ?></th>
                <th><?= __('Privacy') ?></th>
                <th><?= __('Dob') ?></th>
                <th><?= __('Diet') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($event->participants as $participants): ?>
                <tr>
                    <td><?= h($participants->id) ?></td>
                    <td><?= h($participants->name) ?></td>
                    <td><?= h($participants->surname) ?></td>
                    <td><?= h($participants->email) ?></td>
                    <td><?= h($participants->tel) ?></td>
                    <td><?= h($participants->privacy) ?></td>
                    <td><?= h($participants->dob) ?></td>
                    <td><?= h($participants->diet) ?></td>                    
                    <td class="actions">
                        <?= $this->Html->link('', ['controller' => 'Participants', 'action' => 'view', $participants->id], ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']) ?>
                        <?= $this->Html->link('', ['controller' => 'Participants', 'action' => 'edit', $participants->id], ['title' => __('Edit'), 'class' => 'btn btn-default glyphicon glyphicon-pencil']) ?>
                        <?= $this->Form->postLink('', ['controller' => 'Participants', 'action' => 'delete', $participants->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participants->id), 'title' => __('Delete'), 'class' => 'btn btn-default glyphicon glyphicon-trash']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="panel-body">no related Participants</p>
    <?php endif; ?>
</div>
