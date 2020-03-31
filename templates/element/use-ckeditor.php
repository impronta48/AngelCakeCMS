<?php echo $this->Html->script('ckeditor/ckeditor', ['block' => true]); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery', ['block' => true]); ?>
<!-- configurazione custom di ckeditor che sovrascrive quella standard -->
<?php //echo $this->Html->script('bikesquare.ckeditor.config', ['block' => true]); ?>


<?php $this->Html->scriptStart(array('block' => true)); ?>
jQuery(document).ready(function($){
    $(".jquery-ckeditor").ckeditor();
});
<?php $this->Html->scriptEnd();