<?php

use Cake\Routing\Router;

$role = $this->Identity->get('role');
?>

<?php if ($role == 'admin') : ?>
  <small><a href="<?= Router::url([
                    'prefix' => 'Admin', 'controller' => 'Blocks', 'action' => 'edit', $id,
                    '?' => ['referrer' => Router::url()]
                  ]) ?>">[Edit Block]</a></small>
<?php endif ?>

<?= $block ?>