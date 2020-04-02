<?php
    //By default we cannot neither edit nor delete
    $_canEdit = false;
    $_canDelete= false;
    //If parameter was passed we take the parameter
    if (!empty($canDelete))
    {
      $_canDelete= $canDelete;
    }
    if (!empty($canEdit))
    {
      $_canEdit= $canEdit;
		}
		if(empty($id))
		{
			$id = null;
		}
		if(empty($small_size))
		{
			$small_size = [370, 320];
		}
		if(empty($large_size))
		{
			$large_size = [720, 640];
		}
?>



	<div class="gallery-wrapper box-layout">
		<div class="row">
			<?php foreach ($images as $img): ?>
			<div class="col-md-3 col-xs-6">
				<div class="gallery-image-wrapper">
					<div class="image">
						<img src="<?= $this->Image->resizedUrl($img, $small_size[0],$small_size[1], 90) ?>" >
						<?php if ($large_size[0]>0): ?>
							<div class="opacity"><a data-fancybox="project" href="<?= $this->Image->resizedUrl($img, $large_size[0],$large_size[1], 90)?>" class="zoom-view"><i class="fa fa-search" aria-hidden="true"></i></a></div>
						<?php endif ?>
					</div>
					<?php if ($_canDelete): ?>
						<?php
							// devo estrarre l'ultimo token dell'url (il nome dell'immagine) altrimenti passerei
								// un url relativo che verrebbe interpretato come un set di parametri
								echo $this->Html->link('Elimina', ['action' => 'removeFile', '?'=>['fname'=> $img]], ['confirm' => 'Vuoi cancellare questa immagine?']);
						?>
					<?php endif; ?>
				</div> <!-- /.gallery-image-wrapper -->
			</div> <!-- /.col- -->
			<?php endforeach ?>
		</div> <!-- /.row -->
	</div> <!-- /.gallery-wrapper -->
