<br>
<div class="container">
<div class="well">
  <div class="row">
	  <div class="col-md-12">
    <?php echo $this->Form->create(null,
        [   'type'    => 'get',
            'inputDefaults' => array(
            'div' => 'form-group ',
            'action'=>'index'
            ),
            'class' => 'form-inline',
    ]); ?>
    <div class="ricerca">
        <?php echo $this->Form->control('destination_id',['label'=>'Sito Locale','div' => 'col col-md-3','value'=>$destination_id, 'options'=>$destinations, 'empty'=>'---'] ); ?>
    </div>
    <div class="ricerca">
        <?php echo $this->Form->control('q',['label'=>'Cerca Articolo','div' => 'col col-md-3','value'=>$q]); ?>
    </div>
    <a href="<?= Cake\Routing\Router::url(['action' => 'add'])?>" class="btn btn-aggiungi btn-success"><i class="fa fa-plus-square"> Aggiungi Articolo</i></a>
    <?php echo $this->Form->submit('Filtra',['class' => 'btn btn-filtra btn-primary']); ?>
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
                <?= $this->Html->link('', ['action' => 'view', $article->slug], ['title' => __('View'), 'class' => 'btn btn-primary glyphicon glyphicon-eye-open']) ?>
                <?= $this->Html->link('', ['action' => 'edit', $article->id], ['title' => __('Edit'), 'class' => 'btn btn-primary glyphicon glyphicon-pencil']) ?>
                <?= $this->Form->postLink('', ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete # {0}?', $article->slug), 'title' => __('Delete'), 'class' => 'btn btn-primary glyphicon glyphicon-trash']) ?>
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
</div>

