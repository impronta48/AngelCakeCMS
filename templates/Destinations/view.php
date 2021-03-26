<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Destination $destination
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Destination'), ['action' => 'edit', $destination->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Destination'), ['action' => 'delete', $destination->id], ['confirm' => __('Are you sure you want to delete # {0}?', $destination->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Destinations'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Destination'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="destinations view content">
            <h3><?= h($destination->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($destination->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Slug') ?></th>
                    <td><?= h($destination->slug) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nazione Id') ?></th>
                    <td><?= h($destination->nazione_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nomiseo') ?></th>
                    <td><?= h($destination->nomiseo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($destination->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Caparra') ?></th>
                    <td><?= $this->Number->format($destination->caparra) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($destination->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($destination->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Published') ?></th>
                    <td><?= $destination->published ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Show In List') ?></th>
                    <td><?= $destination->show_in_list ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Payment Conf') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($destination->payment_conf)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Articles') ?></h4>
                <?php if (!empty($destination->articles)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Slug') ?></th>
                            <th><?= __('Body') ?></th>
                            <th><?= __('Published') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Destination Id') ?></th>
                            <th><?= __('Archived') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Promoted') ?></th>
                            <th><?= __('Slider') ?></th>
                            <th><?= __('Keywords') ?></th>
                            <th><?= __('Description') ?></th>
                            <th><?= __('Url Canonical') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($destination->articles as $articles) : ?>
                        <tr>
                            <td><?= h($articles->id) ?></td>
                            <td><?= h($articles->title) ?></td>
                            <td><?= h($articles->slug) ?></td>
                            <td><?= h($articles->body) ?></td>
                            <td><?= h($articles->published) ?></td>
                            <td><?= h($articles->created) ?></td>
                            <td><?= h($articles->modified) ?></td>
                            <td><?= h($articles->destination_id) ?></td>
                            <td><?= h($articles->archived) ?></td>
                            <td><?= h($articles->user_id) ?></td>
                            <td><?= h($articles->promoted) ?></td>
                            <td><?= h($articles->slider) ?></td>
                            <td><?= h($articles->keywords) ?></td>
                            <td><?= h($articles->description) ?></td>
                            <td><?= h($articles->url_canonical) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Articles', 'action' => 'view', $articles->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Articles', 'action' => 'edit', $articles->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Articles', 'action' => 'delete', $articles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $articles->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
