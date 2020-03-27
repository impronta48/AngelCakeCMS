
<?= $this->Form->create($participant); ?>
<fieldset>
    <legend><?= __('Edit {0}', ['Participant']) ?></legend>
    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('surname');
    echo $this->Form->control('email');
    echo $this->Form->control('tel');
    echo $this->Form->control('note');
    echo $this->Form->control('event_id', ['options' => $events]);
    ?>

    <?= $this->Form->control('dob',['type'=>'date','dateFormat'=>'DMY','maxYear'=>2005,'minYear'=>1940,'label'=>'Data di Nascita']); ?>
    <?= $this->Form->control('pob',['label'=>'Luogo di Nascita']); ?>
    <?= $this->Form->control('destination_id',['label'=>'Sito YEPP di Appartenenza']); ?>


    <?= $this->Form->control('forum_id_prima_scelta',['label'=>'Prima Scelta']); ?>
    <?= $this->Form->control('forum_id_seconda_scelta',['label'=>'Seconda Scelta']); ?>

    <?= $this->Form->control('city',['label'=>'Città di Residenza']); ?>
    <?= $this->Form->control('diet',['label'=>'Intolleranze Alimentari o Regime Alimentare']); ?>

    <?= $this->Form->control('privacy',['type'=>'checkbox','label'=>'Autorizzo YEPP Italia al trattamento dei dati per le sole comunicazioni legate alla vita associativa']); ?>
</fieldset>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
