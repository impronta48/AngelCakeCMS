

<?= $this->Form->create($participant); ?>
<fieldset>
    <legend><?= __('Add {0}', ['Participant']) ?></legend>
    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('surname');
    echo $this->Form->control('email');
    echo $this->Form->control('tel');
    echo $this->Form->control('privacy');
    echo $this->Form->control('dob',['label'=>'Data di nascita',['type'=>'date','dateFormat'=>'DMY','maxYear'=>2005,'minYear'=>1993]]);
    echo $this->Form->control('diet');
    echo $this->Form->control('note');
    echo $this->Form->control('event_id', ['options' => $events]);
    ?>
</fieldset>
<?= $this->Form->button(__("Add")); ?>
<?= $this->Form->end() ?>
