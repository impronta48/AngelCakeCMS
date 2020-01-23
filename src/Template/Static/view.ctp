<?php 

 $this->assign('title', $title);
 if(isset($keywords)) {
    $this->Html->meta(
    'keywords',
    $keywords,
    ['block'=>true]
    );
 }
 
 if(isset($description)) {
    $this->Html->meta(
        'description',
        $description,
        ['block'=>true]
    );
 }
?>

<div class="page-header parallax dark largest larger-desc" data-bgattach="images/backgrounds/aboutus-header.jpg" data-0="background-position:50% 0px;" data-500="background-position:50% -100%">
    <div class="container" data-0="opacity:1;" data-top="opacity:0;">
        <div class="row">
            <div class="col-md-6">
                <h1><?= $title ?></h1>
                <p class="page-header-desc"><?= $description?></p>
            </div><!-- End .col-md-6 -->
            <div class="col-md-6">
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a></li>
                    <li class="active"><?= $category ?></li>
                </ol>
            </div><!-- End .col-md-6 -->
        </div><!-- End .row -->
    </div><!-- End .container -->
</div><!-- End .page-header -->

<div class="page-header custom larger larger-desc">
                <div class="container">
                    <div class="row">
                        <div class="col-md-10">
                            <h1> </h1>
                            <p class="page-header-desc"></p>
                        </div><!-- End .col-md-6 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
</div><!-- End .page-header -->

<div class="container">
            
                <?php if (isset($copertina)): ?>
                <div class="entry-media">
                    <figure>                        
                        <?php if(strpos($copertina,'.jpg')||strpos($copertina,'.png')):?>
                            <img src="<?= $this->Image->resizedUrl($copertina, 800, 600, 95);  ?>" alt="<?= $description?>" >                            
                        <?php endif; ?>

                        <?php if(strpos($copertina,'youtube')):?>                        
                            <iframe width="560" height="315" src="<?= $copertina ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <?php endif;?>
                    </figure>
                </div><!-- End .entry-media -->
                <?php endif ?>
               
                <?php
                $first=substr( strip_tags($body),0,1);
                $body_first=strpos($body,$first);
                $body[$body_first]=' ';
                ?>
                <p>
                    <span class="dropcap"><?= $first;?></span>
                    <?= $body?>
                </p>

            
</div><!-- End .container -->