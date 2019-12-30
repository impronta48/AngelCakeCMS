 <?php echo $this->Html->script('owl.carousel.min',['inline'=>false]); ?>

 <div class="owl-carousel aboutus-slider">
 	<?php foreach ($images as $i): ?>
 		<img src="<?= $i['src'] ?>" 
 			 alt="<?= $i['alt'] ?>">
	<?php endforeach; ?>                     
</div><!-- End .owl-carousel -->



<?= $this->Html->scriptStart(['inline'=>false])?>
   /* Aboutus.html - About Us Slider */
            $('.owl-carousel.aboutus-slider').owlCarousel({
                loop:false,
                margin:0,
                responsiveClass:true,
                nav:false,
                autoplay:true,
                autoplayTimeout:1000,
                autoplayHoverPause:true
                navText: ['<i class="fa fa-angle-left">', '<i class="fa fa-angle-right">'],
                dots: true,
                items:1
            });

<?= $this->Html->scriptEnd()?>