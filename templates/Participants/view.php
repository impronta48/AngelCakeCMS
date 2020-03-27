<div class="panel panel-default">
    <!-- Panel header -->
    <div class="panel-heading">
        <h3 class="panel-title"><?= h($participant->name) ?></h3>
    </div>
    <table class="table table-striped" cellpadding="0" cellspacing="0">
        <tr>
            <td><?= __('Name') ?></td>
            <td><?= h($participant->name) ?></td>
        </tr>
        <tr>
            <td><?= __('Surname') ?></td>
            <td><?= h($participant->surname) ?></td>
        </tr>
        <tr>
            <td><?= __('Email') ?></td>
            <td><?= h($participant->email) ?></td>
        </tr>
        <tr>
            <td><?= __('Tel') ?></td>
            <td><?= h($participant->tel) ?></td>
        </tr>
        <tr>
            <td><?= __('Privacy') ?></td>
            <td><?= h($participant->privacy) ?></td>
        </tr>
        <tr>
            <td><?= __('Diet') ?></td>
            <td><?= h($participant->diet) ?></td>
        </tr>
        <tr>
            <td><?= __('Event') ?></td>
            <td><?= $participant->has('event') ? $this->Html->link($participant->event->title, ['controller' => 'Events', 'action' => 'view', $participant->event->id]) : '' ?></td>
        </tr>
        <tr>
            <td><?= __('Id') ?></td>
            <td><?= $this->Number->format($participant->id) ?></td>
        </tr>
        <tr>
            <td><?= __('pob') ?></td>
            <td><?= h($participant->pob) ?></td>
        </tr>
        <tr>
            <td><?= __('Dob') ?></td>
            <td><?= h($participant->dob) ?></td>
        </tr>
        <tr>
            <td><?= __('city') ?></td>
            <td><?= h($participant->city) ?></td>
        </tr>
        <tr>
            <td><?= __('address') ?></td>
            <td><?= h($participant->address) ?></td>
        </tr>
        <tr>
            <td><?= __('facebook') ?></td>
            <td><?= h($participant->facebook) ?></td>
        </tr>
        <tr>
            <td><?= __('ente') ?></td>
            <td><?= h($participant->ente) ?></td>
        </tr>
        <tr>
            <td><?= __('forum_id_prima_scelta') ?></td>
            <td><?= h($participant->forum_id_prima_scelta) ?></td>
        </tr>
        <tr>
            <td><?= __('forum_id_seconda_scelta') ?></td>
            <td><?= h($participant->forum_id_seconda_scelta) ?></td>
        </tr>
        <tr>
            <td><?= __('Created') ?></td>
            <td><?= h($participant->created) ?></td>
        </tr>
        <tr>
            <td><?= __('Modified') ?></td>
            <td><?= h($participant->modified) ?></td>
        </tr>
        <tr>
            <td><?= __('Note') ?></td>
            <td><?= $this->Text->autoParagraph(h($participant->note)); ?></td>
        </tr>
    </table>
</div>

