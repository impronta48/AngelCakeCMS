<?= $this->Form->create($tag); ?>
<fieldset>
    <legend><?= __('Edit {0}', ['Tag']) ?></legend>
    <?php
    echo $this->Form->control('title');
    echo $this->Form->control('articles._ids', ['options' => $articles]);
    ?>
</fieldset>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
