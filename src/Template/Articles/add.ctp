<?php echo $this->Html->script('ckeditor/ckeditor', ['block' => true]); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery', ['block' => true]); ?>
<!-- configurazione custom di ckeditor che sovrascrive quella standard -->
<?php echo $this->Html->script('bikesquare.ckeditor.config', ['block' => true]); ?>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 */
?>
<?php
$this->extend('../Layout/TwitterBootstrap/dashboard');

$this->start('tb_actions');
?>
    <li><?= $this->Html->link(__('List Articles'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Tags'), ['controller' => 'Tags', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Tag'), ['controller' => 'Tags', 'action' => 'add']) ?> </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?= $this->Html->link(__('List Articles'), ['action' => 'index']) ?></li>
    <li><?= $this->Html->link(__('List Tags'), ['controller' => 'Tags', 'action' => 'index']) ?> </li>
    <li><?= $this->Html->link(__('New Tag'), ['controller' => 'Tags', 'action' => 'add']) ?> </li>
</ul>
<?php
$this->end();
?>
<?= $this->Form->create($article,['type'=>'file']); ?>
<fieldset>
    <legend><?= __('Edit {0}', ['Article']) ?></legend>
    <?php echo $this->Form->control('title'); ?>
    
    <div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> Immagine di copertina</h3>
    </div>
    <div class="panel-body">
      <?php echo $this->Form->file('newcopertina', [                            
                            'label'=> 'Immagine di copertina',
                            'after'=> 'In questo campo puoi caricare una sola immagine',
        ]); ?>
    </div>
    </div>

    <?php    
    echo $this->Form->control('body',['class'=>'jquery-ckeditor']);
    echo $this->Form->control('destination_id', ['empty' => '---']);    
    ?>
</fieldset>

<div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> Immagini associate a questo articolo</h3>
    </div>
    <div class="panel-body">
      <?php echo $this->Form->file('newgallery. ', [
                            'multiple' => 'multiple',
                            'label'=> 'Immagini del articolo',
                            'after'=> 'In questo campo puoi caricare più immagini, semplicemente selezionandone di più',
        ]); ?>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> File allegati a questo articolo</h3>
    </div>
    <div class="panel-body">          
     <?php echo $this->Form->file('newallegati. ', [
                            'multiple' => 'multiple',
                            'label'=> 'Allegati dell\'articolo',
                            'after'=> 'In questo campo puoi caricare più file, semplicemente selezionandone di più',
        ]); ?>
    </div>
</div>
<?= $this->Form->control('published',['label'=>'Pubblicato']); ?>
<?= $this->Form->control('archived',['label'=>'Archiviato']); ?>
<?= $this->Form->control('user_id'); ?>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>

<?php $this->Html->scriptStart(array('block' => true)); ?>
$(function () {
	$(".jquery-ckeditor").ckeditor({
		customConfig: 'js/bikesquare.ckeditor.config.js'
	});
});
<?php $this->Html->scriptEnd();