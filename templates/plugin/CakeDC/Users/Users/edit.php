<?php

/**
 * Copyright 2010 - 2017, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2017, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use Cake\Core\Configure;

$Users = ${$tableAlias};
?>
<div class="row">
  <div class="actions col col-md-3">
    <h3><?= __d('CakeDC/Users', 'Actions') ?></h3>
    <ul class="side-nav">
      <li>
        <?php
        echo $this->Form->postLink(
          __d('CakeDC/Users', 'Delete'),
          ['action' => 'delete', $Users->id],
          ['confirm' => __d('CakeDC/Users', 'Are you sure you want to delete # {0}?', $Users->id)]
        );
        ?>
      </li>
      <li><?= $this->Html->link(__d('CakeDC/Users', 'List Users'), ['action' => 'index']) ?></li>
    </ul>
  </div>

  <div class="users form col col-md-9">
    <?= $this->Form->create($Users); ?>
    <fieldset>
      <legend><?= __d('CakeDC/Users', 'Edit User') ?></legend>
      <?php
      echo $this->Form->control('username', ['label' => __d('CakeDC/Users', 'Username')]);
      echo $this->Form->control('email', ['label' => __d('CakeDC/Users', 'Email')]);
      echo $this->Form->control('first_name', ['label' => __d('CakeDC/Users', 'First name')]);
      echo $this->Form->control('last_name', ['label' => __d('CakeDC/Users', 'Last name')]);
      echo $this->Form->control('role', ['label' => 'Ruolo', 'options' => ['user' => 'user', 'admin' => 'admin']]);
      echo $this->Form->control('active', [
        'label' => __d('CakeDC/Users', 'Active')
      ]);
      ?>
    </fieldset>
    <?= $this->Form->button(__d('CakeDC/Users', 'Submit')) ?>
    <?= $this->Form->end() ?>
    <?php if (Configure::read('Users.GoogleAuthenticator.login')) : ?>
      <fieldset>
        <legend>Reset Google Authenticator</legend>
        <?= $this->Form->postLink(
          __d('CakeDC/Users', 'Reset Google Authenticator Token'),
          [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'resetGoogleAuthenticator', $Users->id
          ],
          [
            'class' => 'btn btn-danger',
            'confirm' => __d(
              'CakeDC/Users',
              'Are you sure you want to reset token for user "{0}"?',
              $Users->username
            )
          ]
        );
        ?>
      </fieldset>
    <?php endif; ?>
  </div>
</div>