<div class="row pb-3 items-end">
      <div class="col-md-12 ">
        <a href="<?= Cake\Routing\Router::url(['action' => 'add']) ?>" class="btn btn-outline-primary float-right mt-4"><i class="bi bi-plus-square"></i> Aggiungi tag</a>
      </div>
    </div>


<table class="table table-striped" cellpadding="0" cellspacing="0">
  <thead>
    <tr>
      <th><?= $this->Paginator->sort('id'); ?></th>
      <th><?= $this->Paginator->sort('label'); ?></th>
      <th><?= $this->Paginator->sort('created'); ?></th>
      <th><?= $this->Paginator->sort('modified'); ?></th>
      <th class="actions"><?= __('Actions'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tags as $tag) : ?>
      <tr>
        <td><?= $this->Number->format($tag->id) ?></td>
        <td><?= h($tag->label) ?></td>
        <td><?= h($tag->created) ?></td>
        <td><?= h($tag->modified) ?></td>
        <td class="actions">
          <?= $this->Html->link('', ['action' => 'view', $tag->id], ['title' => __('View'), 'class' => 'btn btn-default bi bi-eye']) ?>
          <?= $this->Html->link('', ['action' => 'edit', $tag->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
          <?= $this->Form->postLink('', ['action' => 'delete', $tag->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tag->id), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>