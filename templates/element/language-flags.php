<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?php $url = Router::reverseToArray($this->request); ?>
<?php foreach (Configure::read('I18n.languages') as $lang) : ?>
    <li class="m-0">
        <?php $url['lang'] = $lang;
        unset($url['?']); ?>
        <a class="pr-2" href="<?= Router::url($url) ?>">
            <?php $path = substr($lang, 0, 2) ?>
            <?= $this->Html->image("flags/{$path}.png", ['alt' => $path]) ?>
        </a>
    </li>
<?php endforeach; ?>