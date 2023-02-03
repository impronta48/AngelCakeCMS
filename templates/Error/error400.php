<?php

use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Routing\Router;

if (!Configure::read('debug')) :
  $this->layout = 'error';
  $this->layout = 'dev_error';

  $this->assign('title', $message);
  $this->assign('templateName', 'error400.ctp');

  $this->start('file');
?>
  <?php if (!empty($error->queryString)) : ?>
    <p class="notice">
      <strong>SQL Query: </strong>
      <?= h($error->queryString) ?>
    </p>
  <?php endif; ?>
  <?php if (!empty($error->params)) : ?>
    <strong>SQL Query Params: </strong>
    <?php Debugger::dump($error->params) ?>
  <?php endif; ?>
  <?= $this->element('auto_table_warning') ?>
  <?php
  if (extension_loaded('xdebug')) :
    xdebug_print_function_stack();
  endif;

  $this->end();
  ?>
  <h2><?= h($message) ?></h2>
  <p class="error">
    <strong><?= __d('cake', 'Error') ?>: </strong>
    <?= __d('cake', 'The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?>
  </p>
<?php endif; ?>

<?php
$g = Router::url("/");
if (!Configure::read('debug')) {
  header("Refresh: 5; URL=$g");
}
?>

<div class="page-header parallax dark larger2x larger-desc" data-bgattach="images/backgrounds/page-header.jpg" data-0="background-position:50% 0px;" data-500="background-position:50% -100%">
  <div class="container" data-0="opacity:1;" data-top="opacity:0;">
    <div class="row">
      <div class="col-md-6">
        <h1><?= $code ?> Page</h1>
        <p class="page-header-desc"><?= h($message) ?></p>
      </div><!-- End .col-md-6 -->
      <div class="col-md-6">
        <ol class="breadcrumb">
          <li><a href="index.html">Home</a></li>
          <li class="active"><?= $code ?></li>
        </ol>
      </div><!-- End .col-md-6 -->
    </div><!-- End .row -->
  </div><!-- End .container -->
</div><!-- End .page-header -->