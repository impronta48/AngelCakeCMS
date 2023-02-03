<div class="page-header dark larger larger-desc">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h1>Elenco</h1>
                            <p class="page-header-desc">Tutte le pagine in questa cartella</p>
                        </div><!-- End .col-md-6 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
</div><!-- End .page-header -->

<div class="container">
    <div class="row">
        <div class="col-md-12">
        <?php foreach($files as $f) :?>
            <article class="entry">
                    <?php 
                    $monthNum  = substr( $f['dati']['date'],5,2);
                    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                    $monthName = $dateObj->format('M');
                    ?>
                <span class="entry-date"><?= substr( $f['dati']['date'],8)?><span><?= $monthName ?></span></span>
                <span class="entry-format"><i class="fa fa-file-image-o"></i></span>
                <div class="row">
                    <div class="col-md-6">
                        <div class="entry-media">
                            <figure>
                            <?php if(!isset($f['dati']['copertina'])): ;?>
                                <?= $this->Html->img("/images/static/BikeSquare_Logo.png?w=520&h=290",['alt'=>'<?= $description?>']) ?>
                            <?php endif?>
                            <?php if(strpos($f['dati']['copertina'],'.jpg')||strpos($f['dati']['copertina'],'.png')):?>
                                <?= $this->Html->img("/images/{$f['dati']['copertina']}?w=1070&h=597",['alt'=>'<?= $description?>']) ?>                            
                        <?php endif; ?>
                        <?php if(strpos($f['dati']['copertina'],'youtube')):?>
                         <iframe id="youtube" width="520" height="290" src="<?= $f['dati']['copertina']?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <?php endif;?>
                            </figure>
                        </div><!-- End .entry-media -->
                    </div><!-- End .col-md-6 -->
                    <div class="col-md-6">
                        <?php $url='/static/view/blog/'.basename($f['file'],'.md');
                        
                        //dd($url);?>
                        <h2 class="entry-title"><a href="<?= $url?>" target="_blank"><?= $f['dati']['title'] ?> </a></h2>
                        <div class="entry-content">
                            <p><?= $f['dati']['description'] ?></p>
                        </div><!-- End .entry-content -->
                    </div><!-- End .col-md-6 -->
                </div><!-- End .row -->
            </article>
        <?php endforeach ?>
</div>