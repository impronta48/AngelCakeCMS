<?php

use Cake\Core\Configure;

$v = $this->fetch('vue');
$d = Configure::read('debug');

// Carico le librerie statiche
if ($d) {
  echo $this->Html->script("node_modules/vue/dist/vue.js");
  echo $this->Html->script("node_modules/bootstrap-vue/dist/bootstrap-vue.js");
} else {
  echo $this->Html->script("node_modules/vue/dist/vue.min.js");
  echo $this->Html->script("node_modules/bootstrap-vue/dist/bootstrap-vue.min.js");
}

echo $this->Html->script('node_modules/bootstrap-vue/dist/bootstrap-vue-icons.min.js');
echo $this->Html->script('node_modules/axios/dist/axios.min');

// Carico l'asset mix
$p = $this->request->getParam('prefix') ? $this->request->getParam('prefix') . '/' : '';

if ($v === "mix") {
  if ($this->request->getParam('action') !== "display") {    
    $vue_name = "mix/" . $p. $this->request->getParam('controller') . '/' . $this->request->getParam('action');
  } else {
    $pageName = $this->request->getParam('pass.0');
    //Se il nome pagina contiene un punto allora il primo pezzo è il nome del plugin
    if (strpos($pageName, '.') !== false) {
      $pageName = explode('.', $pageName);
      $this->plugin = $pageName[0];
      $pageName = $pageName[1];
    }
    $vue_name = "mix/" . $p . $this->request->getParam('controller') . '/' . $pageName;
  }
  
} else if (empty($v)) {
  $vue_name = "vue/" . $p. $this->request->getParam('controller') . '/' . $this->request->getParam('action');
} else {
  $vue_name = "vue/$v";
}

$vue_path = ltrim($this->Url->script($vue_name), '/');
$css_path = ltrim($this->Url->css($vue_name), '/');

if (!empty($this->plugin) && file_exists(ROOT . "/plugins/{$this->plugin}/webroot/{$vue_path}")) {
  if ($v === "mix"){
    echo $this->AssetMix->script("{$this->plugin}.$vue_name");
  } else { 
    echo $this->Html->script("{$this->plugin}.$vue_name");
  }
} else if (file_exists(WWW_ROOT . $vue_path)) {
  if ($v === "mix") {
    echo $this->AssetMix->script($vue_name);

  } else {
    echo $this->Html->script($vue_name);
  }
} else {
  echo $this->Html->script($this->Url->script("vue/app.js"));
}

if (!empty($this->plugin) && file_exists(ROOT . "/plugins/{$this->plugin}/webroot/{$css_path}")) {
  if ($v === "mix") {
    echo $this->AssetMix->css("{$this->plugin}.$vue_name");
  } else {
    echo $this->Html->css("{$this->plugin}.$vue_name");
  }
} else if (file_exists(WWW_ROOT . $css_path)) {
  if ($v === "mix") {
    echo $this->Html->css($vue_name);
    //echo $this->AssetMix->css($vue_name);

  } else {
    echo $this->Html->css($vue_name);
  }
}
