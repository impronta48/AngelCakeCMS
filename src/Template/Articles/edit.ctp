<?php echo $this->Html->script('ckeditor/ckeditor', ['block' => true]); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery', ['block' => true]); ?>

<?php $this->assign('title', 'Edit: ' . $article->title); ?>

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
    <li><?= $this->Html->link(__('View Article'), ['action' => 'view', $article->slug]) ?></li>
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $article->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $article->id)]
    )
    ?>
    </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?= $this->Html->link(__('View Article'), ['action' => 'view', $article->slug]) ?></li>
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $article->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $article->id)]
    )
    ?>
    </li>
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
      <?php if (isset($article->copertina)): ?>
        <?= $this->Image->resize($article->copertina, 180,180,[], 90) ?>
        <a href="<?= Cake\Routing\Router::url(['controller'=>'Articles','action'=>'remove_file', 'fname' => $article->copertina])?>" 
           title="Elimina" 
           class="btn btn-danger btn-xs">
          Elimina
        </a>
      <?php endif; ?>
      <?php echo $this->Form->file('newcopertina', [                            
                            'label'=> 'Immagine di copertina',
                            'after'=> 'In questo campo puoi caricare una sola immagine',
        ]); ?>
    </div>
    </div>

    <?php
    echo $this->Form->control('slug');
    echo $this->Form->control('renewSlug',['type'=>'checkbox']);
    echo $this->Form->control('body',['class'=>'jquery-ckeditor']);
    echo $this->Form->control('destination_id', ['empty' => '---']);    
    ?>
</fieldset>

<div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> Immagini associate a questo articolo</h3>
    </div>
    <div class="panel-body">
      <?php if (isset($article->gallery)): ?>
      <?php echo $this->element('img_gallery', [
                'images' => $article->gallery,
                'id' => $article->id,                
                'canEdit'=>true,
                'canDelete'=>true,
                'small_size' => [180,180],
                'large_size'=>[-1,-1]
        ]); ?>

      <?php endif; ?>
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
      <?php if (isset($article->allegati)): ?>
        <ul>
            <?php foreach ($article->allegati as $file): ?>
                <li>
                    <a href="<?= $file ?>"><i class="fa fa-file-o"></i> <?= basename($file) ?></a>
                    <a href="<?= Cake\Routing\Router::url(['controller'=>'Articles','action'=>'remove_file', 'fname'=> $file])?>" 
                            title="Elimina" 
                            class="btn btn-danger btn-xs">
                            Elimina
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif ?>
     <?php echo $this->Form->file('newallegati. ', [
                            'multiple' => 'multiple',
                            'label'=> 'Allegati dell\'articolo',
                            'after'=> 'In questo campo puoi caricare più file, semplicemente selezionandone di più',
        ]); ?>
    </div>
</div>
<?= $this->Form->control('published',['label'=>'Pubblicato']); ?>
<?= $this->Form->control('archived',['label'=>'Archiviato']); ?>
<?= $this->Form->control('promoted',['label'=>'Promosso in Home Page']); ?>
<?= $this->Form->control('modified',['label'=>'Ultima Modifica','type'=>'text']); ?>
<?= $this->Form->control('user_id'); ?>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>

<?php $this->Html->scriptStart(array('block' => true)); ?>
jQuery(document).ready(function($){
    $(".jquery-ckeditor").ckeditor();
});
<?php $this->Html->scriptEnd();