<?php
	use \App\Controller\AttachmentsController;
	// This is needed, if not set default to 'dropzone'

	if (!isset($field)) {
		$field = 'dropzone';
	}

	// Make array of existing files to pass them to Vue
	$existingImages = [];
	if (isset($files)) {
		foreach ($files as $img) {
			$fname = basename($img);
			$existingImages[] = [
				'name' => $fname,
				'size' => filesize(WWW_ROOT . $img),
				'thumbnail_url' => "/images/$img?w=150&h=300&fit=crop", // TODO non-image files cannot be previewed by glide!
				'raw_url' => $img,
			];
		}
	}
?>

<file-uploader
	field="<?= $field ?>"
	:files='JSON.parse(`<?= isset($files) ? json_encode($existingImages) : "[]" ?>`)'
	model="<?= isset($model) ? $model : 'Attachments' ?>"
	destination="<?= isset($destination) ? $destination : 'TEMP' ?>"
	id="<?= isset($id) ? $id : uniqid() ?>"
	:multiple="<?= isset($multiple) && $multiple ? 'true' : 'false' ?>"
	:temporary="<?= isset($temp) && $temp ? 'true' : 'false' ?>"
	filetype="<?= isset($filetype) ? $filetype : null ?>"
></file-uploader>

<?= $this->Html->css('/js/node_modules/vue2-dropzone/dist/vue2Dropzone.min.css', ['block' => true]); ?>
<?= $this->Html->script('/js/node_modules/vue2-dropzone/dist/vue2Dropzone.js', ['block' => true]); ?>
<?= $this->Html->script('vue/element/upload.js', ['block' => true]); ?>