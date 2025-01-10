<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?php $url = Router::reverseToArray($this->request); ?>
<?php $url['lang'] = $lang; ?>
<?php $path = substr($lang, 0, 2) ?>
<b-nav-item-dropdown class="pl-lg-4">
    <template v-slot:button-content>
        <?= $this->Html->image("flags/{$path}.png", ['alt' => $path]) ?>
    </template>
    <?php $path = substr($lang, 0, 2) ?>
    <?php foreach (Configure::read('I18n.languages') as $lang) : ?>
        <?php $url['lang'] = $lang;
        unset($url['?']); ?>
        <b-dropdown-item href="<?= Router::url($url) ?>" class="w0">
            <?php $path = substr($lang, 0, 2) ?>
            <?= $this->Html->image("flags/{$path}.png", ['alt' => $path]) ?>
        </b-dropdown-item>
    <?php endforeach; ?>
</b-nav-item-dropdown>