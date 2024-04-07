<?php	

	$this->assign('vue', 'mix');
	// This is needed, if not set default to 'dropzone'

	if (!isset($field)) {
		$field = 'dropzone';
	}

	// Make array of existing files to pass them to Vue
	$existingImages = [];
	if (isset($files)) {
		foreach ($files as $img) {
			if (!empty($img)) {
				//Strip trailing slash
				$img = rtrim($img, '/');				
				$fname = basename($img);
				if (file_exists(WWW_ROOT . $img)){
					$existingImages[] = [
						'name' => $fname,
						'size' => filesize(WWW_ROOT . $img),
						'thumbnail_url' => "/images/$img?w=200&h=200&fit=crop", // TODO non-image files cannot be previewed by glide!
						'raw_url' => $img,
					];
				}
			}
		}
	}
?>

<file-uploader
	field="<?= $field ?>"
	:files='JSON.parse(`<?= isset($files) ? json_encode($existingImages) : "[]" ?>`)'
	model="<?= isset($model) ? $model : 'Attachments' ?>"
 	destination="<?= isset($destination->slug) ? $destination->slug : 'null' ?>"
	id="<?= isset($id) ? $id : uniqid() ?>"
	:multiple="<?= isset($multiple) && $multiple ? 'true' : 'false' ?>"
	:temporary="<?= isset($temp) && $temp ? 'true' : 'false' ?>"
	filetype="<?= isset($filetype) ? $filetype : null ?>"
	:convert="<?= !isset($convert) || $convert ? 'true' : 'false' ?>"
></file-uploader>

<?= $this->Html->css('/js/node_modules/vue2-dropzone/dist/vue2Dropzone.min.css', ['block' => true]); ?>
<?= $this->Html->script('/js/node_modules/vue2-dropzone/dist/vue2Dropzone.js', ['block' => true]); ?>
