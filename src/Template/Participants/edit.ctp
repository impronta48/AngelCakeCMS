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
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $participant->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id)]
    )
    ?>
    </li>
    <li><?= $this->Html->link(__('List Participants'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add']) ?> </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $participant->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id)]
    )
    ?>
    </li>
    <li><?= $this->Html->link(__('List Participants'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add']) ?> </li>
</ul>
<?php
$this->end();
?>
<?= $this->Form->create($participant); ?>
<fieldset>
    <legend><?= __('Edit {0}', ['Participant']) ?></legend>
    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('surname');
    echo $this->Form->control('email');
    echo $this->Form->control('tel');
    echo $this->Form->control('note');
    echo $this->Form->control('event_id', ['options' => $events]);    
    ?>

    <?= $this->Form->control('dob',['type'=>'date','dateFormat'=>'DMY','maxYear'=>2005,'minYear'=>1940,'label'=>'Data di Nascita']); ?>
    <?= $this->Form->control('pob',['label'=>'Luogo di Nascita']); ?>                                        
    <?= $this->Form->control('destination_id',['label'=>'Sito YEPP di Appartenenza']); ?>
    
    
    <?= $this->Form->control('forum_id_prima_scelta',['label'=>'Prima Scelta']); ?>
    <?= $this->Form->control('forum_id_seconda_scelta',['label'=>'Seconda Scelta']); ?>

    <?= $this->Form->control('city',['label'=>'Città di Residenza']); ?>
    <?= $this->Form->control('diet',['label'=>'Intolleranze Alimentari o Regime Alimentare']); ?>

    <?= $this->Form->control('privacy',['type'=>'checkbox','label'=>'Autorizzo YEPP Italia al trattamento dei dati per le sole comunicazioni legate alla vita associativa']); ?>
</fieldset>        
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
