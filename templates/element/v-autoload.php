<?php
$v = $this->fetch('vue');
if (empty($v)) {
  $vue_name = "vue/" . $this->request->getParam('controller') . '/' . $this->request->getParam('action');
} else {
  $vue_name = "vue/$v";
}

$vue_path = $this->Url->script($vue_name);

if (!empty($this->plugin) && file_exists(ROOT . "/plugins/{$this->plugin}/webroot{$vue_path}")) {
  echo $this->Html->script("{$this->plugin}.$vue_name");
} else if (file_exists(WWW_ROOT . $vue_path)) {
  echo $this->Html->script($vue_path);
} else {
  echo $this->Html->script($this->Url->script("vue/app.js"));
}
