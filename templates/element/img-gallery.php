<?php $this->Html->script("/js/node_modules/spotlight.js/dist/spotlight.bundle.js", ['block' => 'script']); ?>

<div class="row">
  <div class="col">
    <?php foreach ($images as $img) : ?>

      <a class="spotlight" href="<?= "/images/$img?w=1042&fm=webp" ?>">
        <img src="<?= "/images/$img?h=150&w=150&fit=crop&fm=webp" ?>" class="img-thumbnail img-fluid">
      </a>

    <?php endforeach ?>
  </div> <!-- /.col- -->
</div> <!-- /.row -->