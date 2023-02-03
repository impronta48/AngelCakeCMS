<?php
// OVerride del plugin di CakeDC (Davide e Massimo 26/6/2019)

use Cake\Core\Configure;

$this->layout = 'admin';
?>
<div class="container">
  <div class="users form">
    <?= $this->Flash->render('auth') ?>
    <?= $this->Form->create() ?>
    <fieldset>
      <legend><?= __d('CakeDC/Users', 'Please enter your username and password') ?></legend>
      <?= $this->Form->control('username', ['label' => __d('CakeDC/Users', 'Username'), 'required' => true]) ?>
      <?= $this->Form->control('password', ['label' => __d('CakeDC/Users', 'Password'), 'required' => true]) ?>
      <?php
      if (Configure::read('Users.reCaptcha.login')) {
        echo $this->User->addReCaptcha();
      }
      if (Configure::read('Users.RememberMe.active')) {
        echo $this->Form->control(Configure::read('Users.Key.Data.rememberMe'), [
          'type' => 'checkbox',
          'label' => __d('CakeDC/Users', 'Remember me'),
          'checked' => Configure::read('Users.RememberMe.checked')
        ]);
      }
      ?>
      <?php
      $registrationActive = Configure::read('Users.Registration.active');
      if ($registrationActive) {
        echo $this->Html->link(__d('CakeDC/Users', 'Register'), ['action' => 'register']);
      }
      if (Configure::read('Users.Email.required')) {
        if ($registrationActive) {
          echo ' | ';
        }
        echo $this->Html->link(__d('CakeDC/Users', 'Reset Password'), ['action' => 'requestResetPassword']);
      }
      ?>
    </fieldset>
    <?= implode(' ', $this->User->socialLoginList()); ?>
    <?= $this->Form->button(__d('CakeDC/Users', 'Login')); ?>
    <?= $this->Form->end() ?>
  </div>
</div>