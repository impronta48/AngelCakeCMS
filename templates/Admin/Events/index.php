<div class="container mt-3">
  <h1>Eventi</h1>


  <?php echo $this->Form->create(
    null,
    [
      'type'    => 'get',
      'inputDefaults' => array(
        'div' => 'form-group '
      ),
      'class' => 'form-horizontal',
    ]
  ); ?>
  <div class="row">
    <div class="col-md-6">
      <?php echo $this->Form->input('q', ['label' => 'Cerca Evento', 'value' => $q]); ?>
    </div>
    <div class="col-md-3">
      <?php echo $this->Form->submit('Filtra', ['class' => 'btn btn-filtra']); ?>
    </div>
    <div class="col-md-3">
      <a href="<?= Cake\Routing\Router::url(['action' => 'add']) ?>" class="btn btn-outline-primary float-right"><i class="bi bi-plus-square"></i> Aggiungi Evento</a>
    </div>
  </div>
  <?php echo $this->Form->end(); ?>

  <div class="row mt-3">
    <table class="table table-striped">
      <thead>
        <tr>
          <th><?= $this->Paginator->sort('id'); ?></th>
          <th><?= $this->Paginator->sort('title', 'Titolo'); ?></th>
          <th><?= $this->Paginator->sort('max_pax', 'Max Pax'); ?></th>
          <th><?= $this->Paginator->sort('place', 'Luogo'); ?></th>
          <th><?= $this->Paginator->sort('destination_id', 'Destination'); ?></th>
          <th><?= $this->Paginator->sort('start_time', 'Inizio'); ?></th>
          <th><?= $this->Paginator->sort('end_time', 'Fine'); ?></th>
          <th class="actions"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($events as $event) : ?>
          <tr>
            <td><?= $this->Number->format($event->id) ?></td>
            <td><?= h($event->title) ?></td>
            <td><?= $this->Number->format($event->max_pax) ?></td>
            <td><?= h($event->place) ?></td>
            <td>
              <?php if ($event->destination) : ?>
                <?= $event->destination->name ?>
              <?php else : ?>
                --
              <?php endif; ?>
            </td>
            <td><?= h($event->start_time) ?></td>
            <td><?= h($event->end_time) ?></td>
            <td class="actions">
              <?= $this->Html->link('', ['action' => 'view', $event->id], ['title' => __('View'), 'class' => 'btn btn-default btn btn-default bi bi-eye']) ?>
              <?= $this->Html->link('', ['action' => 'edit', $event->id], ['title' => __('Edit'), 'class' => 'btn btn-default btn btn-default bi bi-pencil']) ?>
              <?= $this->Html->link('', ['prefix' => false, 'action' => 'subscribe', $event->slug], ['title' => __('Subscribe'), 'class' => 'btn btn-default btn btn-default bi bi-users']) ?>
              <?= $this->Form->postLink('', ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
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
</div>