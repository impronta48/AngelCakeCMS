<br>
<div class="container">
<a href="<?= Cake\Routing\Router::url(['action' => 'add']); ?>" class="btn btn-primary">Aggiungi Destination</a>

<table class="table table-striped" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id'); ?></th>
            <th><?= $this->Paginator->sort('name'); ?></th>
            <th><?= $this->Paginator->sort('slug'); ?></th>
            <th class="actions"><?= __('Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($destinations as $destination): ?>
        <tr>
            <td><?= $this->Number->format($destination->id) ?></td>
            <td><?= h($destination->name) ?></td>
            <td><?= h($destination->slug) ?></td>
            <td class="actions">
                <?= $this->Html->link('', ['action' => 'view', $destination->slug], ['title' => __('View'), 'class' => 'btn btn-default fa fa-eye']) ?>
                <?= $this->Html->link('', ['action' => 'edit', $destination->id], ['title' => __('Edit'), 'class' => 'btn btn-default fa fa-pencil']) ?>
                <?= $this->Form->postLink('', ['action' => 'delete', $destination->id], ['confirm' => __('Are you sure you want to delete # {0}?', $destination->id), 'title' => __('Delete'), 'class' => 'btn btn-default fa fa-trash']) ?>
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