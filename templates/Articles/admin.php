<br>
<div class="container">
    <div class="well">
        <?php echo $this->Form->create(
                    null,
                    [
                        'type'    => 'get',
                        'inputDefaults' => array(
                            'div' => 'form-group '
                        ),
                        'class' => 'form-inline',
                    ]
                ); ?>
        <div class="row">
    
                <div class="col-md-3">
                    <?php echo $this->Form->control('destination_id', ['label' => 'Categoria', 'div' => 'col col-md-3', 'value' => $destination_id, 'options' => $destinations, 'empty' => '---']); ?>
                </div>
                <div class="col-md-3">
                    <?php echo $this->Form->control('q', ['label' => 'Cerca Articolo', 'div' => 'col col-md-3', 'value' => $q]); ?>
                </div>
                <div class="col-md-2">
                    <?php echo $this->Form->submit('Filtra', ['class' => 'btn btn-filtra btn-primary mt-3']); ?>                            
                </div>            
                <div class="col-md-4">
                    <a href="<?= Cake\Routing\Router::url(['action' => 'add']) ?>" class="btn"><i class="fa fa-plus-square"></i> Aggiungi Articolo</a>
                </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <table class="table table-striped" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id'); ?></th>
                <th><?= $this->Paginator->sort('user_id'); ?></th>
                <th><?= $this->Paginator->sort('title'); ?></th>
                <th><?= $this->Paginator->sort('destination_id', ['label'=>'Categoria']); ?></th>
                <th><?= $this->Paginator->sort('modified'); ?></th>
                <th class="actions"><?= __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article) : ?>
                <tr>
                    <td><?= $this->Number->format($article->id) ?></td>
                    <td>
                        <?php if ($article->user) : ?>
                            <?= $article->user->username ?>
                        <?php else : ?>
                            --
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= h($article->title) ?>
                        <?php if ($article->published): ?>
                            <span class="badge badge-success">Pubblicato</span>
                        <?php else: ?>                        
                            <span class="badge badge-danger">Non-Pubblicato</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($article->destination) : ?>
                            <?= $article->destination->name ?>
                        <?php else : ?>
                            --
                        <?php endif; ?>
                    </td>
                    <td><?= h($article->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link('', '/articles/view/' . $article->slug, ['title' => __('View'), 'class' => 'btn btn-primary fa fa-eye']) ?>
                        <?= $this->Html->link('', ['action' => 'edit', $article->id], ['title' => __('Edit'), 'class' => 'btn btn-primary fa fa-pencil']) ?>
                        <?= $this->Form->postLink('', ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete # {0}?', $article->slug), 'title' => __('Delete'), 'class' => 'btn btn-primary fa fa-trash']) ?>
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