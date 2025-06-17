
<?= $this->Form->create($tag); ?>
<fieldset>
    <legend><?= __('Add {0}', ['Tag']) ?></legend>
    <?php
    echo $this->Form->control('title');
    echo $this->Form->control('articles._ids', ['options' => $articles]);
    ?>
</fieldset>
<?= $this->Form->button(__("Add")); ?>
<?= $this->Form->end() ?>
