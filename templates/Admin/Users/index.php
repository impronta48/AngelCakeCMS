<?php

use Cake\Core\Configure;
?>
<div class="row">

  <div class="col-md-12">
    <div class="float-right">
      <a href="<?= Cake\Routing\Router::url(['action' => 'add']) ?>" class="btn btn-outline-primary float-right mt-4"><i class="bi bi-plus-square"></i> Aggiungi Utente</a>
    </div>
  </div>


</div>
<table class="table table-striped mt-3">
  <thead>
    <th>Account Gmail</th>
    <th>Gruppo</th>
    <th>Destination</th>
    <th>Azioni</th>
  </thead>

  <?php foreach ($users as $u) : ?>
    <tr>
      <td><?= $u->gmail ?></td>
      <td><?= Configure::read('groups')[$u->group_id] ?></td>
      <td><?= empty($u->destination) ? '*' : $u->destination->name ?></td>
      <td class="actions">
        <?= $this->Html->link('', ['action' => 'edit', $u->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
        <?= $this->Form->postLink('', ['action' => 'delete', $u->id], ['confirm' => __('Are you sure you want to delete # {0}?', $u->gmail), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
      </td>
    </tr>
  <?php endforeach ?>

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