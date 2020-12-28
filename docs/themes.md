# Themes
You create themes the usual CakePHP way.
Simply create a folder into the plugins/ folder with your favourite name CamelCased.

Then define the theme in the config/sites/default/settings.php
in the variable 'Theme' 

You can override all the template and also provide extra functionalities.

## Extending the admin page 
In order to extend the admin page you should create an element in your plugin
with the name
`
  v-admin-extra-main-icons.php
`
In the file you should provide simple menu-items, in a theme block called
_extra-main-icons_

```php
  <?php

  use Cake\Routing\Router;

  /*******************************
   * These are extra icons+titles that your plugin brings in the main
   * admin home-page. Every "action" should be included in a div 
   * with the following classes
   * col-md-2 text-center mt-5
   ********************************/
  ?>

  <?php $this->append('extra-main-icons'); ?>
  <div class="col-md-2 text-center mt-5">

    <a href="<?= Router::url(['controller' => 'Bandi', 'action' => 'index']) ?>" class="titoloAdmin">
      <h4>Bandi</h4>
    </a>

    <a href="<?= Router::url(['controller' => 'Bandi', 'action' => 'index']) ?>">
      <img src="/img/admin/download.png" class="img-responsive icona">
    </a>
  </div>
  <?php $this->end(); ?>
```

## Extending the admin menu
In order to extend the admin menu with your entities, your plugin should provide
one element named
`
  v-admin-extra-main-menu.php
`
In the file you should provide simple menu-items, in a theme block called
_extra-main-menu_

```php
  <?php

  use Cake\Routing\Router;

  $this->append('extra-main-menu'); ?>
  <b-nav-item href="<?= Router::url(['controller' => 'Bandi', 'action' => 'index']) ?>">Bandi</b-nav-item>
  <?php $this->end(); ?>
```
