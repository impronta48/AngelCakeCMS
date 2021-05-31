<!doctype html>

<?php
use Cake\Core\Configure;
?>

<html lang="<?= Configure::read('App.language') ?>">

<head>
  <?= $this->Html->charset() ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

  <title><?= $this->fetch('title') ?> | AngelCake Admin</title>

  <!-- Bootstrap core CSS -->
  <?= $this->Html->css('/js/node_modules/bootstrap/dist/css/bootstrap.min') ?>
  <?= $this->Html->css('/js/node_modules/bootstrap-vue/dist/bootstrap-vue.css') ?>
  <?= $this->Html->css('/js/node_modules//bootstrap-icons/font/bootstrap-icons.css') ?>

  <!-- Load polyfills to support older browsers -->
  <script src="//polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver" crossorigin="anonymous"></script>

  <!-- Custom styles for this template -->
  <?= $this->Html->css('admin-style') ?>

  <?= $this->fetch('meta') ?>
  <?= $this->fetch('css') ?>


</head>

<body class="d-flex flex-column min-vh-100">
  <div id="app">

    <div v-cloak class="d-flex flex-column min-vh-100">

      <div class="xv-cloak--inline">
        <!-- Parts that will be visible before compiled your HTML -->
        <div class="xspinner"></div>
      </div>

      <div class="xv-cloak--hidden">
        <!-- Parts that will be visible After compiled your HTML -->
        <?= $this->element('v-admin-header'); ?>
        <main role="main" class="container">
          <div class="mt-2"><?= $this->Flash->render() ?></div>
          <?= $this->fetch('content') ?>

        </main><!-- /.container -->
      </div>

      <?= $this->element('admin-footer'); ?>
    </div>
  </div>

  <!-- Bootstrap core JavaScript
    ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

  <?= $this->Html->script('node_modules/vue/dist/vue.js') ?>
  <?= $this->Html->script('node_modules/bootstrap-vue/dist/bootstrap-vue.js') ?>
  <?= $this->Html->script('node_modules/bootstrap-vue/dist/bootstrap-vue-icons.min.js') ?>
  <?= $this->Html->script('ckeditor/ckeditor') ?>
  <?= $this->Html->script('node_modules/ckeditor4-vue/dist/ckeditor') ?>

  <?= $this->fetch('script') ?>
  <?= $this->element('v-autoload')  //Carica automaticamente lo script /theme/js/vue/{Controller}/{action}.js
  ?>
  <?= $this->Html->script('add-ckeditor.js') ?>

</body>

</html>