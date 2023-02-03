<h1>Utenti</h1>

<?= $this->Html->link('Aggiungi Utente', ['action' => 'add'], ['class' => 'btn btn-outline-primary float-right mt-4']) ?>


<table class="table table-striped mt-3">
  <thead>
    <tr>
      <th><?= $this->Paginator->sort('username', __d('CakeDC/Users', 'Username')) ?></th>
      <th><?= $this->Paginator->sort('email', __d('CakeDC/Users', 'Email')) ?></th>
      <th><?= $this->Paginator->sort('first_name', __d('CakeDC/Users', 'First name')) ?></th>
      <th><?= $this->Paginator->sort('last_name', __d('CakeDC/Users', 'Last name')) ?></th>
      <th><?= $this->Paginator->sort('last_name', __d('CakeDC/Users', 'Role')) ?></th>
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
        <td class="actions">
          <?= $this->Html->link('', ['action' => 'changePassword', $user->id], ['title' => __('Change Password'), 'class' => 'btn btn-default bi bi-key']) ?>
          <?= $this->Html->link('', ['action' => 'edit', $user->id], ['title' => __('Edit'), 'class' =>  'btn btn-default bi bi-pencil']) ?>
          <?= $this->Form->postLink('', ['action' => 'delete', $user->id], ['confirm' => __d('CakeDC/Users', 'Are you sure you want to delete # {0}?', $user->id), 'class' => 'btn btn-default bi bi-trash']) ?>
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