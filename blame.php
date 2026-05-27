<?php
$manifestFileContent = file_get_contents('/home/filippo/www/angelcake/webroot/mix-manifest.json');
$manifest = json_decode($manifestFileContent, true);
$path = "/js/mix/Admin/Tags/edit.js";
var_dump(isset($manifest[$path]));
var_dump(array_keys($manifest));
