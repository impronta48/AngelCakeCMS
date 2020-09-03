<!-- 
=============================================
	Blog Grid
============================================== 
-->
<div class="blog-grid section-margin-top">
	<div class="container">		

		<div class="row">
			<?php  if (isset($articles)) : ?>
			<?php foreach ($articles as $article) : ?>
			
			<div class="col-md-4 col-xs-6">				
				<div class="single-blog-grid hover-effect-one">
					<div class="image">
						<img src="<?= "/images/{$article->copertina}?w=370&h=260" ?>" alt="<?= h($article->title) ?>">
						<div class="title">
							<h5><a href="<?= \Cake\Routing\Router::url(['controller'=>'articles','action'=>'view',$article->slug]) ?>"><?= h($article->title) ?></a></h5>							
						</div>
					</div> <!-- /.image -->
					<div class="text">
            <h5>
              <span><?= Cake\I18n\Time::parse($article->modified)->format('d M Y'); ?></span>
            </h5>
            <p><?= $this->Text->truncate(
                                strip_tags($article->body),
                                90,
                                [
                                    'ellipsis' => '...',
                                    'exact' => false,
                                    'html' => false
                                ]
                            ); ?> 
                            <a href="<?= \Cake\Routing\Router::url(['controller'=>'articles','action'=>'view',$article->slug]) ?>">
                               [leggi tutto]
                            </a>
                            </p>  
					</div> <!-- /.text -->
				</div> <!-- /.single-blog-grid -->
			</div> <!-- /.col- -->

			<?php endforeach ?>
			<?php endif;?>
		</div> <!-- /.row -->



	</div> <!-- /.container -->
</div> <!-- /.blog-grid -->