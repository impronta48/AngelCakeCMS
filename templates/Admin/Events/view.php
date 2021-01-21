<div class="container">
  <?= $this->element('v-admin-navbar', ['event' => $event]); ?>

  <div class="panel panel-default">
    <!-- Panel header -->
    <div class="panel-heading">
      <h3 class="panel-title"><?= h($event->title) ?></h3>
    </div>
    <table class="table table-striped" cellpadding="0" cellspacing="0">
      <tr>
        <td><?= __('Title') ?></td>
        <td><?= h($event->title) ?></td>
      </tr>
      <tr>
        <td><?= __('Place') ?></td>
        <td><?= h($event->place) ?></td>
      </tr>
      <tr>
        <td><?= __('Destination') ?></td>
        <td><?= $event->has('destination') ? $this->Html->link($event->destination->name, ['controller' => 'Destinations', 'action' => 'view', $event->destination->id]) : '' ?></td>
      </tr>
      <tr>
        <td><?= __('User') ?></td>
        <td><?= $event->has('user') ? $this->Html->link($event->user->email, ['controller' => 'Users', 'action' => 'view', $event->user->id]) : '' ?></td>
      </tr>
      <tr>
        <td><?= __('Id') ?></td>
        <td><?= $this->Number->format($event->id) ?></td>
      </tr>
      <tr>
        <td><?= __('Max Pax') ?></td>
        <td><?= $this->Number->format($event->max_pax) ?></td>
      </tr>
      <tr>
        <td><?= __('Min Year') ?></td>
        <td><?= $this->Number->format($event->min_year) ?></td>
      </tr>
      <tr>
        <td><?= __('Max Year') ?></td>
        <td><?= $this->Number->format($event->max_year) ?></td>
      </tr>
      <tr>
        <td><?= __('Start Time') ?></td>
        <td><?= h($event->start_time) ?></td>
      </tr>
      <tr>
        <td><?= __('End Time') ?></td>
        <td><?= h($event->end_time) ?></td>
      </tr>
      <tr>
        <td><?= __('Created') ?></td>
        <td><?= h($event->created) ?></td>
      </tr>
      <tr>
        <td><?= __('Modified') ?></td>
        <td><?= h($event->modified) ?></td>
      </tr>
      <tr>
        <td><?= __('Description') ?></td>
        <td><?= $this->Text->autoParagraph(h($event->description)); ?></td>
      </tr>
    </table>
  </div>

  <div class="panel panel-default">
    <!-- Panel header -->
    <div class="panel-heading">
      <h3 class="panel-title">Partecipanti a questo evento</h3>
      <?= $this->Html->link('Scarica XLS', [
        'prefix' => 'Admin',
        'controller' => 'participants',
        'action' => 'index',
        '_ext' => 'xls',
        $event->id
      ], [
        'class' => 'btn btn-primary'
      ]); ?>
    </div>
    <?php if (!empty($event->participants)) : ?>
      <table class="table table-striped">
        <thead>
          <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Name') ?></th>
            <th><?= __('Surname') ?></th>
            <th><?= __('Email') ?></th>
            <th><?= __('Tel') ?></th>
            <th><?= __('Privacy') ?></th>
            <th><?= __('Dob') ?></th>
            <th><?= __('Diet') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($event->participants as $participants) : ?>
            <tr>
              <td><?= h($participants->id) ?></td>
              <td><?= h($participants->name) ?></td>
              <td><?= h($participants->surname) ?></td>
              <td><?= h($participants->email) ?></td>
              <td><?= h($participants->tel) ?></td>
              <td><?= h($participants->privacy) ?></td>
              <td><?= h($participants->dob) ?></td>
              <td><?= h($participants->diet) ?></td>
              <td class="actions">
                <?= $this->Html->link('', ['controller' => 'Participants', 'action' => 'view', $participants->id], ['title' => __('View'), 'class' => 'btn btn-default bi bi-eye']) ?>
                <?= $this->Html->link('', ['controller' => 'Participants', 'action' => 'edit', $participants->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
                <?= $this->Form->postLink('', ['controller' => 'Participants', 'action' => 'delete', $participants->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participants->id), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else : ?>
      <p class="panel-body">no related Participants</p>
    <?php endif; ?>
  </div>
</div>