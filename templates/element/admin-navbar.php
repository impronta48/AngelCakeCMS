
<ul class="nav nav-tabs" style="margin-bottom: 1em">
    <?php if(isset($event->id)) : ?>
        <li class="active"><?= $this->Html->link(__('Edit'), ['action' => 'edit', $event->id]) ?> </li>
    <?php else: ?>
        <li class="active"><?= $this->Html->link(__('New'), ['action' => 'add']) ?> </li>
    <?php endif ?>

    <li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?> </li>

    <?php if (isset($event->slug)): ?>
        <li><?= $this->Html->link(__('View'), ['action' => 'view', $event->slug]) ?> </li>
    <?php else: ?>
        <li><?= $this->Html->link(__('View'), ['action' => 'view', $event->id]) ?> </li>
    <?php endif ?>
    <li><?= $this->Html->link(__('List'), ['action' => 'index']) ?> </li>

    <li><?= $this->Html->link(__('Admin Home'), '/pages/admin') ?> </li>
</ul>
