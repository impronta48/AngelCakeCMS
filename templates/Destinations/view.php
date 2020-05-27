<?php $this->assign('title', $destination->name); ?>

<!-- 
=============================================
    Theme Inner Banner
============================================== 
-->

<div class="theme-inner-banner">
  <div class="opacity">
    <h1>YEPP <?= h($destination->name) ?></h1>
  </div> <!-- /.opacity -->
</div> <!-- /.theme-inner-banner -->

<div class="container">
  <div class="row">
    <div class="col-md-12 course-details-content">
      <div class="course-info-data">
        <div class="clearfix">
          <h2 class="float-left"><i class="flaticon-device"></i>YEPP
            <?= h($destination->name) ?></h2>
          <ul class="float-right course-value">
            <?php if ($destination->facebook) :?>
            <li>
              <a href="<?=$destination->facebook ?>" target="_blank" class="white">
                <i class="fa fa-2x fa-facebook-square"></i>
              </a>
              &nbsp;
            </li>
            <?php endif ?>

            <?php if ($destination->instagram) :?>
            <li>
              <a href="<?=$destination->instagram?>" target="_blank" class="white">
                <i class="fa fa-2x fa-instagram"></i>
              </a>
            </li>
            <?php endif ?>
          </ul>
        </div>
        <div class="clearfix bottom-content">
          <ul class="course-schedule float-left">
            <?php if ($destination->anno_attivazione) :?><li>Anno di Attivazione : <b><?=$destination->anno_attivazione ?></b></li><?php endif ?>
            <?php if ($destination->comuni) :?><li>Comuni Coinvolti: <b><?=$destination->comuni ?></b></li><?php endif ?>
            <?php if ($destination->tipologia) :?><li>Tipologia : <b><?=$destination->tipologia ?></b></li><?php endif ?>
            <?php if ($destination->lc) :?><li>Coordinatore Locale : <b><?=$destination->lc ?></b></li><?php endif ?>
            <?php if ($destination->ef) :?><li>Facilitatore Valutazione : <b><?=$destination->ef ?></b></li><?php endif ?>
            <?php if ($destination->presidente) :?><li>Presidente : <b><?=$destination->presidente ?></b></li><?php endif ?>
            <?php if ($destination->coach) :?><li>Coach : <b><?=$destination->coach ?></b></li><?php endif ?>
            <?php if ($destination->fondazione_locale) :?><li>Fondazione Locale : <b><?=$destination->fondazione_locale ?></b></li><?php endif ?>
            <?php if (0 && $destination->email) :?><li>e-mail : <b><?=$destination->email ?></b></li><?php endif ?>
          </ul>
          <a href="/pages/contact" class="float-right theme-line-button">Entra in Contatto</a>
        </div>
      </div>
      <?php if ($destination->descrizione) :?>
        <h4>Descrizione </h4>
        <p>
        <?=  preg_replace('/font-family.+?;/', "", preg_replace("#<font[^>]*>#is", '', $destination->descrizione)); ?>
        </p>
      <?php endif ?>
    </div>
  </div>
</div>

<div class="our-courses section-margin-top section-margin-bottom">
  <div class="container">
    <div class="row" style="min-height: 15em">
      <?php $copertina1 = null; ?>
      <?php foreach ($articles as $article) : ?>
      <?php if (is_null($copertina1)) {
        $copertina1 = $article->copertina; 
      }?>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="single-course-block">
          <figure>
            <a
              href="<?= \Cake\Routing\Router::url(['controller'=>'articles','action'=>'view',$article->slug]) ?>">
              <div class="image">
                <img
                  src="<?= "{$article->copertina}?w=370&h=260" ?>"
                  alt="<?= h($article->title) ?>">
              </div>
            </a>
          </figure>
          <div class="text-box">
            <h5><i class="fa fa-newspaper-o"></i>
              <a
                href="<?= \Cake\Routing\Router::url(['controller'=>'articles','action'=>'view',$article->slug]) ?>">
                <?= $article->title ?>
              </a>
            </h5>
            <p><?= $this->Text->truncate(
                                strip_tags($article->body),
                                140,
                                [
                                    'ellipsis' => '...',
                                    'exact' => false,
                                    'html' => false
                                ]
                            ); ?>
              <a
                href="<?= \Cake\Routing\Router::url(['controller'=>'articles','action'=>'view',$article->slug]) ?>">
                [leggi tutto]
              </a>
            </p>
          </div> <!-- /.text-box -->
        </div> <!-- /.single-course-block -->
      </div> <!-- /.col- -->
      <?php endforeach ?>
    </div> <!-- /.row -->
    
    <div class="row">
                            
      <div class="col-sm-6">        
        <?php // = $this->Paginator->numbers() ?>
      </div>
                            
      <div class="col-md-6"> 

        <?php $testoBottone = 'Archivio News Sezione '  . $destination->name . ' <i class="fa fa-arrow-circle-right"></i>' ;
        if ($archived)
        {
          $testoBottone = 'Ultime News Sezione ' . $destination->name . ' <i class="fa fa-arrow-circle-up"></i>';
        }                             
        ?>
        <a
          href="<?= \Cake\Routing\Router::url(['controller'=>'destinations','action'=>'view',$destination->slug, '?'=>['archive'=>!$archived]]) ?>"
          class="theme-solid-button pull-right"
        >
        <i class="fa fa-newspaper-o"></i> <?= $testoBottone ?> 
        </a>
      </div>
    </div>

  </div> <!-- /.container -->
</div> <!-- /.our-courses -->

<style>
.theme-inner-banner {
  background: url('<?= $copertina1 ?>') no-repeat center/cover;
  background-position: center center;
  width: 100%;    
}
</style>