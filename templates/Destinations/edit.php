<?php $this->assign('title', 'Destination Edit: ' . $destination->title); ?>
<?= $this->element('use-ckeditor'); ?>


<div class="container">
<?= $this->element('admin-navbar', ['event'=>$destination]); ?>



<?= $this->Form->create($destination); ?>
<fieldset>

    <?php
    echo $this->Form->control('name');
    echo $this->Form->control('slug');
    echo $this->Form->control('show_in_list');
    echo $this->Form->control('facebook');
    echo $this->Form->control('instagram');
    echo $this->Form->control('lc');
    echo $this->Form->control('ef');
    echo $this->Form->control('anno_attivazione');
    echo $this->Form->control('comuni');
    echo $this->Form->control('tipologia');
    echo $this->Form->control('presidente');
    echo $this->Form->control('coach');
    echo $this->Form->control('fondazione_locale');
    echo $this->Form->control('descrizione',['class'=>'jquery-ckeditor']);
    echo $this->Form->control('email');
    echo $this->Form->control('chiuso');
    ?>
</fieldset>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
</div>
