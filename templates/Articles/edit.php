<?php $this->assign('title', 'Article Edit: ' . $article->title); ?>
<?= $this->element('use-ckeditor'); ?>

<div class="container">
<?= $this->element('admin-navbar', ['event'=>$article]); ?>
<br>

<?= $this->Form->create($article,[
            'type'=>'file',
            'class' => 'form'
    ]); ?>

<fieldset>

    <?php echo $this->Form->control('title'); ?>

    <div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-image"></i> Immagine di copertina</h3>
    </div>
    <div class="panel-body">
      <?php if (isset($article->copertina)): ?>
        <?= $this->Html->img('/images/'. $article->copertina . '?w=180&h=180') ?>
        
        <a href="<?= Cake\Routing\Router::url(['controller'=>'Articles','action'=>'remove_file', '?'=>['fname' => $article->copertina]])?>"
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
      <?php echo $this->element('img-gallery', [
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
                            'label'=> 'Immagini dell\'articolo',
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
                    <a href="<?= Cake\Routing\Router::url(['controller'=>'Articles','action'=>'remove_file', '?'=>['fname'=> $file]])?>"
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
<?= $this->Form->control('slider',['label'=>'Visibile nello Slider']); ?>
<?= $this->Form->control('modified',['label'=>'Ultima Modifica','type'=>'datetime']); ?>
<?= $this->Form->control('user_id'); ?>
<?= $this->Form->hidden('id'); ?>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
</div>

