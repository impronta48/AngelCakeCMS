<?php
// Template di CakeDC Users ovveridato da Davide e Massimo 26/6/2019
?>
<div class="container">
  <div class="actions columns large-2 medium-3">
    <h3><?= __d('CakeDC/Users', 'Actions') ?></h3>
    <ul class="side-nav">
      <li><?= $this->Html->link(__d('CakeDC/Users', 'New {0}', $tableAlias), ['action' => 'add']) ?></li>
    </ul>
  </div>
  <div class="users index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0" class="table table-responsive">
      <thead>
        <tr>
          <th><?= $this->Paginator->sort('username', __d('CakeDC/Users', 'Username')) ?></th>
          <th><?= $this->Paginator->sort('email', __d('CakeDC/Users', 'Email')) ?></th>
          <th><?= $this->Paginator->sort('first_name', __d('CakeDC/Users', 'First name')) ?></th>
          <th><?= $this->Paginator->sort('last_name', __d('CakeDC/Users', 'Last name')) ?></th>
          <th><?= $this->Paginator->sort('last_name', __d('CakeDC/Users', 'Role')) ?></th>
          <th><?= $this->Paginator->sort('last_name', __d('CakeDC/Users', 'Company')) ?></th>
          <th class="actions"><?= __d('CakeDC/Users', 'Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (${$tableAlias} as $user) : ?>
          <tr>
            <td><?= h($user->username) ?></td>
            <td><?= h($user->email) ?></td>
            <td><?= h($user->first_name) ?></td>
            <td><?= h($user->last_name) ?></td>
            <td><?= h($user->role) ?></td>
            <td><?= h($user->company) ?></td>
            <td class="actions">
              <?= $this->Html->link('View', ['action' => 'view', $user->id], ['class' => 'btn btn-default glyphicon glyphicon-eye-open']) ?>
              <?= $this->Html->link('Change', ['action' => 'changePassword', $user->id], ['class' => 'btn btn-default glyphicon fa fa-key ']) ?>
              <?= $this->Html->link('Edit', ['action' => 'edit', $user->id], ['class' =>  'btn btn-default glyphicon glyphicon-pencil']) ?>
              <?= $this->Form->postLink('Delete', ['action' => 'delete', $user->id], ['confirm' => __d('CakeDC/Users', 'Are you sure you want to delete # {0}?', $user->id), 'class' => 'btn btn-default glyphicon glyphicon-trash']) ?>
            </td>
          </tr>

        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="paginator">
      <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __d('CakeDC/Users', 'previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__d('CakeDC/Users', 'next') . ' >') ?>
      </ul>
      <p><?= $this->Paginator->counter() ?></p>
    </div>
  </div>
</div>