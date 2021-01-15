<ul class="nav nav-tabs" style="margin-bottom: 1em">
  <?php if (isset($event->id)) : ?>
    <li class="nav-item active"><?= $this->Html->link(__('Edit'), ['prefix' => 'Admin', 'action' => 'edit', $event->id], ['class' => 'nav-link']) ?> </li>
  <?php else : ?>
    <li class="nav-item active"><?= $this->Html->link(__('New'), ['prefix' => 'Admin', 'action' => 'add'], ['class' => 'nav-link']) ?> </li>
  <?php endif ?>

  <li class="nav-item"><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id), 'class' => 'nav-link'],) ?> </li>

  <?php if (isset($event->slug)) : ?>
    <li class="nav-item"><?= $this->Html->link(__('View'), ['action' => 'view', $event->slug], ['class' => 'nav-link']) ?> </li>
  <?php else : ?>
    <li class="nav-item"><?= $this->Html->link(__('View'), ['action' => 'view', $event->id], ['class' => 'nav-link']) ?> </li>
  <?php endif ?>
  <li class="nav-item"><?= $this->Html->link(__('List'), ['prefix' => 'Admin', 'action' => 'index'], ['class' => 'nav-link']) ?> </li>

  <li class="nav-item"><?= $this->Html->link(__('Admin Home'), '/admin', ['class' => 'nav-link']) ?> </li>
</ul>