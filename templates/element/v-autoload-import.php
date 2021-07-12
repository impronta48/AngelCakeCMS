<?php
$v = $this->fetch('vue');
if ($v === "mix") {
  return; // Temporary, for migration to Asset-Mix
}

echo $this->Html->script("node_modules/vue/dist/vue.js");
echo $this->Html->script("node_modules/bootstrap-vue/dist/bootstrap-vue.js");
echo $this->Html->script("node_modules/bootstrap-vue/dist/bootstrap-vue-icons.js");
echo $this->Html->css("/js/node_modules/bootstrap-vue/dist/bootstrap-vue.css");
echo $this->Html->script('node_modules/axios/dist/axios.min');