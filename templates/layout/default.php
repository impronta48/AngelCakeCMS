<!doctype html>
<html lang="en">
  <head>
	<?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title><?= $this->fetch('title') ?></title>

	<!-- Bootstrap core CSS -->
	<?= $this->Html->css('bootstrap.min') ?>

	<!-- Custom styles for this template -->
	<?= $this->Html->css('style') ?>


    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
  </head>

  <body>
    <?= $this->element('admin-header'); ?>
    <main role="main" class="container">

        <?= $this->Flash->render() ?>
	    <?= $this->fetch('content') ?>

    </main><!-- /.container -->
	  <?= $this->element('admin-footer'); ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

	<?= $this->Html->script('bootstrap.min') ?>
  </body>
</html>