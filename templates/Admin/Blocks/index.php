<?= $this->Html->link(__('New Block'), ['action' => 'add'], ['class' => 'float-right btn btn-outline-primary']) ?>
<br>
<table class="table table-striped mt-3">
  <thead>
    <tr>
      <th scope="col"><?= $this->Paginator->sort('id') ?></th>
      <th scope="col"><?= $this->Paginator->sort('title') ?></th>
      <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($blocks as $block) : ?>
      <tr>
        <td><?= $this->Number->format($block->id) ?></td>
        <td><?= h($block->title) ?></td>
        <td class="actions">
          <?= $this->Html->link(__(''), ['action' => 'edit', $block->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
          <?= $this->Form->postLink(__(''), ['action' => 'delete', $block->id], ['confirm' => __('Are you sure you want to delete # {0}?', $block->id), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div class="paginator">
  <ul class="pagination">
    <?= $this->Paginator->first('<< ' . __('First')) ?>
    <?= $this->Paginator->prev('< ' . __('Previous')) ?>
    <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
    <?= $this->Paginator->next(__('Next') . ' >') ?>
    <?= $this->Paginator->last(__('Last') . ' >>') ?>
  </ul>
  <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
</div>