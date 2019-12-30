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
    <?php echo $this->Form->create('Articolo',
        [   'type'    => 'get',                        
            'inputDefaults' => array(
                'div' => 'form-group ',
                'action'=>'index'
            ),
            'class'         => '',
    ]); ?>
    <div class="ricerca">
        <?php echo $this->Form->input('destination_id',['label'=>'Sito Locale','div' => 'col col-md-3','value'=>$destination_id, 'empty'=>'---']); ?> 
    </div>
    <div class="ricerca">
        <?php echo $this->Form->input('q',['label'=>'Cerca Articolo','div' => 'col col-md-3','value'=>$q]); ?> 
    </div>
    <a href="<?= Cake\Routing\Router::url(['action' => 'add'])?>" class="btn btn-aggiungi"><i class="fa fa-plus-square"> Aggiungi Articolo</i></a>
    <?php echo $this->Form->submit('Filtra',['div' => 'form-group col col-md-1','class' => 'btn btn-filtra']); ?>
    <?php echo $this->Form->end(); ?>
    </div>
    </div>

    </div>
<table class="table table-striped" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id'); ?></th>
            <th><?= $this->Paginator->sort('user_id'); ?></th>
            <th><?= $this->Paginator->sort('title'); ?></th>
            <th><?= $this->Paginator->sort('destination_id'); ?></th>
            <th><?= $this->Paginator->sort('modified'); ?></th>
            <th class="actions"><?= __('Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= $this->Number->format($article->id) ?></td>
            <td>
                <?php if ($article->user) :?>
                    <?= $article->user->username?>
                <?php else : ?>
                    --
                <?php endif ; ?>
            </td>
            <td><?= h($article->title) ?> <?= h($article->published) ?></td>
            <td>
                <?php if ($article->destination) :?>
                    <?= $article->destination->name?>
                <?php else : ?>
                    --
                <?php endif ; ?>
            </td>
            <td><?= h($article->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link('', ['action' => 'view', $article->slug], ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']) ?>
                <?= $this->Html->link('', ['action' => 'edit', $article->id], ['title' => __('Edit'), 'class' => 'btn btn-default glyphicon glyphicon-pencil']) ?>
                <?= $this->Form->postLink('', ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete # {0}?', $article->slug), 'title' => __('Delete'), 'class' => 'btn btn-default glyphicon glyphicon-trash']) ?>
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
