<?php
use Cake\Core\Configure;

$v = $this->fetch('vue');

$d = Configure::read('debug');
if ($d) {
    echo $this->Html->script("node_modules/vue/dist/vue.js");
    echo $this->Html->script("node_modules/bootstrap-vue/dist/bootstrap-vue.js");
} else {
    echo $this->Html->script("node_modules/vue/dist/vue.min.js");
    echo $this->Html->script("node_modules/bootstrap-vue/dist/bootstrap-vue.min.js");
}

echo $this->Html->script('node_modules/bootstrap-vue/dist/bootstrap-vue-icons.min.js');
echo $this->Html->script('node_modules/axios/dist/axios.min');


