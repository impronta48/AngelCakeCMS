<div class="panel panel-default">
  <!-- Panel header -->
  <div class="panel-heading">
    <h3 class="panel-title"><?= h($tag->title) ?></h3>
  </div>
  <table class="table table-striped" cellpadding="0" cellspacing="0">
    <tr>
      <td><?= __('Title') ?></td>
      <td><?= h($tag->title) ?></td>
    </tr>
    <tr>
      <td><?= __('Id') ?></td>
      <td><?= $this->Number->format($tag->id) ?></td>
    </tr>
    <tr>
      <td><?= __('Created') ?></td>
      <td><?= h($tag->created) ?></td>
    </tr>
    <tr>
      <td><?= __('Modified') ?></td>
      <td><?= h($tag->modified) ?></td>
    </tr>
  </table>
</div>

<div class="panel panel-default">
  <!-- Panel header -->
  <div class="panel-heading">
    <h3 class="panel-title"><?= __('Related Articles') ?></h3>
  </div>
  <?php if (!empty($tag->articles)) : ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th><?= __('Id') ?></th>
          <th><?= __('User Id') ?></th>
          <th><?= __('Title') ?></th>
          <th><?= __('Slug') ?></th>
          <th><?= __('Body') ?></th>
          <th><?= __('Published') ?></th>
          <th><?= __('Created') ?></th>
          <th><?= __('Modified') ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tag->articles as $articles) : ?>
          <tr>
            <td><?= h($articles->id) ?></td>
            <td><?= h($articles->user_id) ?></td>
            <td><?= h($articles->title) ?></td>
            <td><?= h($articles->slug) ?></td>
            <td><?= h($articles->body) ?></td>
            <td><?= h($articles->published) ?></td>
            <td><?= h($articles->created) ?></td>
            <td><?= h($articles->modified) ?></td>
            <td class="actions">
              <?= $this->Html->link('', ['controller' => 'Articles', 'action' => 'view', $articles->id], ['title' => __('View'), 'class' => 'btn btn-default bi bi-eye']) ?>
              <?= $this->Html->link('', ['controller' => 'Articles', 'action' => 'edit', $articles->id], ['title' => __('Edit'), 'class' => 'btn btn-default bi bi-pencil']) ?>
              <?= $this->Form->postLink('', ['controller' => 'Articles', 'action' => 'delete', $articles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $articles->id), 'title' => __('Delete'), 'class' => 'btn btn-default bi bi-trash']) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else : ?>
    <p class="panel-body">no related Articles</p>
  <?php endif; ?>
</div>