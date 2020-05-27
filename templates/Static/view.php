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

<div class="page-header custom larger larger-desc">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1><?= $title ?></h1>
                    <p class="page-header-desc"><?= $description?></p>
                </div><!-- End .col-md-8 -->
                <?php if(isset($category)): ?>
                <div class="col-md-4">
                    <ol class="breadcrumb">
                        <li><a href="/">Home</a></li>
                            <li class="active"><?= $category ?></li>
                    </ol> 
                </div><!-- End .col-md-6 -->
                <?php endif ;?>
            </div><!-- End .row -->
        </div><!-- End .container -->
</div><!-- End .page-header -->

<div class="container">
            
                <?php if (isset($copertina)): ?>
                <div class="entry-media">
                    <figure>                        
                        <?php if(strpos($copertina,'.jpg')||strpos($copertina,'.png')):?>
                            <img src="<?= "$copertina?w=800&h=600"  ?>" alt="<?= $description?>" >                            
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