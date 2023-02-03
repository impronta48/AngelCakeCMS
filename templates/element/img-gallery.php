<div class="gallery-wrapper box-layout">
  <div class="row">
    <?php foreach ($images as $img) : ?>
      <div class="col-md-3 col-xs-6 m-3">
        <div class="gallery-image-wrapper">
          <div class="image">
            <b-img thumbnail fluid src="<?= "/images/$img?w=500&h=300&fit=crop&fm=webp" ?>">
          </div>
        </div> <!-- /.gallery-image-wrapper -->
      </div> <!-- /.col- -->
    <?php endforeach ?>
  </div> <!-- /.row -->
</div> <!-- /.gallery-wrapper -->