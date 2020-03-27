<?php
use Cake\Controller\Component\AuthComponent;
?>

<?php $this->assign('title', $article->title); ?>


<!--
=============================================
    Blog Details
==============================================
-->
<div class="blog-list">
    <div class="container">

        <?php if ($user): //Solo se sono loggato posso vedere questo blocco ?>
        <ul class="nav nav-tabs">
            <li><?= $this->Html->link(__('Edit Article'), ['action' => 'edit', $article->id]) ?> </li>
            <li class="active"><a href="#">View Article</a></li>
            <li><?= $this->Form->postLink(__('Delete Article'), ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete # {0}?', $article->id)]) ?> </li>
        </ul>
        <?php endif; ?>
        <br>

        <div class="row">
            <div class="col-lg-9 col-md-8 col-xs-12 blog-details-content">
                <div class="single-blog-list">
                    <div class="image"><img src="<?= $article->copertina ?>" alt="<?= $article->title ?>" class="img-responsive img-rounded"></div>
                    <ul class="post-info">
                        <li>Ultimo Aggiornamento: <?= $article->modified ?></li>
                    </ul>
                    <h1><?= h($article->title) ?></h1>
                    <?=  preg_replace('/font.+?;/', "", preg_replace("#<font[^>]*>#is", '', $article->body)); ?>
                </div> <!-- /.single-blog-list -->

                <!--
                =============================================
                    Our Gallery
                ==============================================
                -->
                <div class="our-gallery section-margin-top">
                <?= $this->element('img-gallery', ['images'=>$article->gallery]); ?>
                </div> <!-- /.our-gallery -->

                <div class="share-content">
                    <div class="text-center">Condividi questo articolo</div>
                    <div class="sharethis-inline-share-buttons"></div>
                </div> <!-- /.share-content -->
            </div> <!-- /.blog-list-content -->

            <!-- ************************ Theme Sidebar *************************** -->
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 theme-sidebar">
                <?php if ($article->allegati) : ?>
                <div class="sidebar-download">
                    <h5>Allegati</h5>
                    <ul>
                    <?php foreach ($article->allegati as $file): ?>
                        <li><a href="<?= $file ?>"><i class="fa fa-file-o"></i> <?= basename($file) ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div> <!-- /.sidebar-download -->
                <?php endif ?>
            </div> <!-- /.theme-sidebar -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div> <!-- /.blog-list -->

