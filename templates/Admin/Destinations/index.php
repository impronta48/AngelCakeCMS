<div class="container mt-3">
  <h1>Destinations / Categorie </h1>

  <div class="well">
    <?= $this->Form->create(
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
      <div class="col-md-7">
        <?php echo $this->Form->control('q', ['label' => '', 'div' => 'col col-md-3', 'value' => $q]); ?>
      </div>
      <div class="col-md-2">
        <?php echo $this->Form->submit('Filtra', ['class' => 'btn btn-filtra btn-primary mt-4']); ?>
      </div>
      <div class="col-md-3">
        <a href="<?= Cake\Routing\Router::url(['action' => 'add']) ?>" class="btn btn-outline-primary float-right mt-4"><i class="bi bi-plus-square"></i> Aggiungi Destination</a>
      </div>
    </div>

    <table class="table table-striped mt-3">
      <thead>
        <tr>
          <th><?= $this->Paginator->sort('id'); ?></th>
          <th><?= $this->Paginator->sort('name'); ?></th>
          <th><?= $this->Paginator->sort('slug'); ?></th>
          <th class="actions"><?= __('Actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($destinations as $destination) : ?>
          <tr>
            <td><?= $this->Number->format($destination->id) ?></td>
            <td><?= h($destination->name) ?></td>
            <td><?= h($destination->slug) ?></td>
            <td class="actions">
              <?= $this->Html->link('', ['prefix' => false, 'action' => 'view', $destination->slug], ['title' => __('View'), 'class' => 'btn btn-default bi bi-eye']) ?>
              <?= $this->Html->link('', ['action' => 'edit', $destination->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
              <?= $this->Html->link('', ['action' => 'delete', $destination->id], ['confirm' => __('Are you sure you want to delete # {0}?', $destination->slug), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
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