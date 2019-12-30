
<div class="container">
    
    <?php if (isset($this->Flash)): ?>
    <div class="row">
        <?= $this->Flash->render(); ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-10">
            <?= $this->fetch('content'); ?>
        </div>
        <div class="col-md-2">
            <?= $this->fetch('tb_sidebar') ?>
        </div>
    </div>
</div>