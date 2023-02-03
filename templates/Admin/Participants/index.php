<div class="container mt-3">

  <h1>Partecipanti</h1>
  <a href="<?= Cake\Routing\Router::url(['action' => 'add']) ?>" class="btn btn-outline-primary float-right mt-4"><i class="bi bi-plus-square"></i> Aggiungi Partecipante</a>


  <table class="table table-striped">
    <thead>
      <tr>
        <th><?= $this->Paginator->sort('id'); ?></th>
        <th><?= $this->Paginator->sort('name'); ?></th>
        <th><?= $this->Paginator->sort('surname'); ?></th>
        <th><?= $this->Paginator->sort('email'); ?></th>
        <th><?= $this->Paginator->sort('tel'); ?></th>
        <th><?= $this->Paginator->sort('event_id'); ?></th>
        <th><?= $this->Paginator->sort('dob'); ?></th>
        <th class="actions"><?= __('Actions'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($participants as $participant) : ?>
        <tr>
          <td><?= $this->Number->format($participant->id) ?></td>
          <td><?= h($participant->name) ?></td>
          <td><?= h($participant->surname) ?></td>
          <td><?= h($participant->email) ?></td>
          <td><?= h($participant->tel) ?></td>
          <td><?= h($participant->event->title) ?></td>
          <td><?= h($participant->dob) ?></td>
          <td class="actions">
            <?= $this->Html->link('', ['action' => 'view', $participant->id], ['title' => __('View'), 'class' => 'btn btn-default bi bi-eye']) ?>
            <?= $this->Html->link('', ['action' => 'edit', $participant->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
            <?= $this->Form->postLink('', ['action' => 'delete', $participant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
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