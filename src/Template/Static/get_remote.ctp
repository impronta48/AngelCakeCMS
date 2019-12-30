<?php
$this->extend('../Layout/TwitterBootstrap/dashboard');
?>

<h2><?= $msg ?></h2>
<hr>
<?= $this->Form->create(); ?>
<?= $this->Form->button(__("Importa Pagine Statiche da OwnCloud")); ?>
<?= $this->Form->end() ?>
