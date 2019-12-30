<?php
/* @var $this \Cake\View\View */
$this->extend('../Layout/TwitterBootstrap/dashboard');
$this->start('tb_actions');
?>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav nav-sidebar">' . $this->fetch('tb_actions') . '</ul>'); ?>
<div class="well">
  <div class="row">
	  <div class="col-md-12">
        <?php echo $this->Form->create('Evento',
            [   'type'    => 'get',                        
                'inputDefaults' => array(
                    'div' => 'form-group ',
                    'action'=>'index'
                ),
                'class'         => '',
        ]); ?>  
        <?php echo $this->Form->input('q',['label'=>'Cerca Evento','div' => 'col col-md-3','value'=>$q]); ?>
        <a href="<?= Cake\Routing\Router::url(['action' => 'add'])?>" class="btn btn-aggiungi"><i class="fa fa-plus-square"> Aggiungi Evento</i></a>
        <?php echo $this->Form->submit('Filtra',['div' => 'form-group col col-md-1','class' => 'btn btn-filtra']); ?>
        <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<table class="table table-striped" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id'); ?></th>
            <th><?= $this->Paginator->sort('title'); ?></th>
            <th><?= $this->Paginator->sort('max_pax'); ?></th>
            <th><?= $this->Paginator->sort('place'); ?></th>
            <th><?= $this->Paginator->sort('destination_id'); ?></th>
            <th><?= $this->Paginator->sort('start_time'); ?></th>
            <th><?= $this->Paginator->sort('end_time'); ?></th>
            <th class="actions"><?= __('Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
        <tr>
            <td><?= $this->Number->format($event->id) ?></td>
            <td><?= h($event->title) ?></td>
            <td><?= $this->Number->format($event->max_pax) ?></td>
            <td><?= h($event->place) ?></td>
            <td>
                <?php if ($event->destination) :?>
                    <?= $event->destination->name?>
                <?php else : ?>
                    --
                <?php endif ; ?>
            </td>
            <td><?= h($event->start_time) ?></td>
            <td><?= h($event->end_time) ?></td>
            <td class="actions">
                <?= $this->Html->link('', ['action' => 'view', $event->id], ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']) ?>
                <?= $this->Html->link('', ['action' => 'edit', $event->id], ['title' => __('Edit'), 'class' => 'btn btn-default glyphicon glyphicon-pencil']) ?>
                <?= $this->Form->postLink('', ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id), 'title' => __('Delete'), 'class' => 'btn btn-default glyphicon glyphicon-trash']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
    <p><?= $this->Paginator->counter() ?></p>
</div>
