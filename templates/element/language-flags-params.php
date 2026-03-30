<?php

use Cake\Core\Configure;
?>

<?php
$languages = (array)Configure::read('I18n.languages');
$currentPath = '/' . ltrim($this->request->getPath(), '/');
$segments = explode('/', trim($currentPath, '/'));

if (!empty($segments) && in_array($segments[0], $languages, true)) {
    array_shift($segments);
}

$pathWithoutLang = '/' . implode('/', $segments);
if ($pathWithoutLang === '/') {
    $pathWithoutLang = '';
}

$queryParams = array_filter(
    (array)$this->request->getQueryParams(),
    function ($value, $key) {
        return is_string($key) && $key !== '' && $key[0] !== '/';
    },
    ARRAY_FILTER_USE_BOTH
);
?>

<?php foreach ($languages as $lang) : ?>
    <li class="m-0">
        <?php
        $url = '/' . $lang . $pathWithoutLang;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
        }
        ?>
        <a class="pr-2" href="<?= h($url) ?>">
            <?php $path = substr($lang, 0, 2) ?>
            <?= $this->Html->image("flags/{$path}.png", ['alt' => $path]) ?>
        </a>
    </li>
<?php endforeach; ?>
