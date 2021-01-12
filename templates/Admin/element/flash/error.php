<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
  $message = h($message);
}
?>

<b-alert dismissible variant="warning">
  <?= $message ?>

  <?php if (isset($params) && isset($params['errors'])) : ?>
    <ul class="">
      <li class="">
        <h5><?= __('The following errors occurred:') ?></h5>
      </li>
      <?php foreach ($params['errors'] as $error) : ?>
        <li class=""><i class="bi bi-edge">error</i><?= h($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</b-alert>