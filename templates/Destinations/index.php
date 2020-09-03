<style>
.theme-inner-banner {
	background: url('<?= \Cake\Routing\Router::url('/img/siti-italiani.jpg') ?>');
    background-size: cover;
    background-position: top center;
    height: 700px;
}
</style>
<?php $this->assign('title', 'I siti locali'); ?>
<!-- 
=============================================
    Theme Inner Banner
============================================== 
-->
<div class="theme-inner-banner">
    <div class="opacity" style="height:700px;">
        <h1>YEPP i siti locali</h1>
    </div> <!-- /.opacity -->
</div> <!-- /.theme-inner-banner -->


<div class="our-courses section-margin-top section-margin-bottom destinations">
	<div class="container">
        <div class="row" style="min-height: 15em">
        <?php foreach ($destinations  as $destination) : ?>
         <?php if (!$destination->chiuso) : ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="single-course-block" > 
                <figure>
                    <div class="image">
                    <img src="<?= "/images/{$article->copertina}?w=370&h=260" ?>" alt="<?= $destination->name ?>">
                    </div>
                </figure>
                    <div class="text-box">
                        <h5><i class="fa fa-newspaper-o"></i>
                            <a href="<?= \Cake\Routing\Router::url(['controller'=>'destinations','action'=>'view',$destination->slug]) ?>">
                                <?= $destination->name ?>
                            </a>
                        </h5>
                        <span class="pull-right">
                        <?php if ($destination->facebook) :?>
                            <a href="<?=$destination->facebook ?>" target="_blank">
                                <i class="fa fa-2x fa-facebook-square"></i>
                            </a>
                            &nbsp;
                        <?php endif ?>
                          
                        <?php if ($destination->instagram) :?>
                            <a href="<?=$destination->instagram?>" target="_blank">
                                <i class="fa fa-2x fa-instagram"></i>
                            </a>
                        <?php endif ?>
                        </span>
                    </div> <!-- /.text-box -->
                </div> <!-- /.single-course-block -->
            </div> <!-- /.col- -->
            <?php endif ?>
        <?php endforeach ?>
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div> <!-- /.our-courses -->
<hr>


<div class="our-courses section-margin-top section-margin-bottom destinations">    
	<div class="container">
        <h2>Esperienze Concluse</h2>
        <br>
        <div class="row" style="min-height: 15em">
        <?php foreach ($destinations  as $destination) : ?>
            <?php if ($destination->chiuso) : ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="single-course-block" > 
                <figure>
                    <div class="image">
                    <img src="<?= "/images/{$article->copertina}?w=370&h=260" ?>" alt="<?= $destination->name ?>">
                    </div>
                </figure>
                    <div class="text-box">
                        <h5><i class="fa fa-newspaper-o"></i>
                            <a href="<?= \Cake\Routing\Router::url(['controller'=>'destinations','action'=>'view',$destination->slug]) ?>">
                                <?= $destination->name ?>
                            </a>
                        </h5>
                        <span class="pull-right">
                        <?php if ($destination->facebook) :?>
                            <a href="<?=$destination->facebook ?>" target="_blank">
                                <i class="fa fa-2x fa-facebook-square"></i>
                            </a>
                            &nbsp;
                        <?php endif ?>
                          
                        <?php if ($destination->instagram) :?>
                            <a href="<?=$destination->instagram?>" target="_blank">
                                <i class="fa fa-2x fa-instagram"></i>
                            </a>
                        <?php endif ?>
                        </span>
                    </div> <!-- /.text-box -->
                </div> <!-- /.single-course-block -->
            </div> <!-- /.col- -->
            <?php endif ?>
        <?php endforeach ?>
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div> <!-- /.our-courses -->