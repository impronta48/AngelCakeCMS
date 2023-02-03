<?php

use Cake\Core\Configure;
?>
<!-- in /templates/Users/login.php -->
<div class="container">
  <div class="users form">
    <?= $this->Flash->render() ?>
    <h3>Login</h3>
    <?= $this->Form->create() ?>
    <fieldset>
      <legend><?= __('Please enter your username and password') ?></legend>
      <?= $this->Form->control('username', ['required' => true]) ?>
      <?= $this->Form->control('password', ['required' => true]) ?>
    </fieldset>
    <?= $this->Form->submit(__('Login'), ['class'=> 'btn btn-primary']); ?>
    <?= $this->Form->end() ?>
    
  </div>
</div>