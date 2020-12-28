<?php
$v = $this->fetch('vue');
if (empty($v)) {
  $vue_name = "vue/" . $this->request->getParam('controller') . '/' . $this->request->getParam('action');
} else {
  $vue_name = "vue/$v";
}

$vue_path = $this->Url->script($vue_name);

if (!file_exists(WWW_ROOT . $vue_path)) {
  $vue_path = $this->Url->script("vue/app.js");
}
echo $this->Html->script($vue_path);
