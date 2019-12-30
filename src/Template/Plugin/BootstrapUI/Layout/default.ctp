<!DOCTYPE html>
<html lang="en">
	<head>
		<?= $this->Html->charset() ?>		
		<!-- For IE -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<!-- For Resposive Device -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?= $this->fetch('title') ?> | YEPP Italia - Youth Empowerment Parteship Programme</title>

		<!-- Favicon -->		
		<link rel="icon" type="image/png" sizes="56x56" href="images/fav-icon/icon.png">

		
		<!-- Main style sheet -->		
	    <?= $this->Html->css('style.css') ?>
    	<?= $this->Html->css('responsive.css') ?>

   	    <?= $this->fetch('meta') ?>
	    <?= $this->fetch('css') ?>
    	

		<!-- Fix Internet Explorer ______________________________________-->

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<script src="vendor/html5shiv.js"></script>
			<script src="vendor/respond.js"></script>
		<![endif]-->

	</head>

	<body>
		
		<div class="main-page-wrapper">

			<!-- ===================================================
				Loading Transition
			==================================================== -->


			<!-- 
			=============================================
				Include header and menu
			============================================== 
			-->
			<?= $this->element('header'); ?>

			<!-- 
			=============================================
				MAIN CONTENT
			============================================== 
			-->
			<?= $this->Flash->render() ?>
		    <?= $this->fetch('content') ?>		   
		    <br>

			<!-- 
			=============================================
				Footer
			============================================== 
			-->
			<?= $this->element('footer'); ?>

	  
	        <!-- Scroll Top Button -->
			<button class="scroll-top tran3s">
				<i class="fa fa-angle-up" aria-hidden="true"></i>
			</button>
			


		<!-- Js File_________________________________ -->
		
		
		<!-- j Query -->
		<?= $this->Html->script('/vendor/jquery.2.2.3.min.js') ?> 
		<!-- Bootstrap -->
		<?= $this->Html->script('/vendor/bootstrap/bootstrap.min.js') ?> 
		<!-- Bootstrap Select JS -->
		<?= $this->Html->script('/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') ?> 
		
		<!-- Camera Slider -->
		<?= $this->Html->script('/vendor/Camera-master/scripts/jquery.mobile.customized.min.js') ?> 
		<?= $this->Html->script('/vendor/Camera-master/scripts/jquery.easing.1.3.js') ?> 
		<?= $this->Html->script('/vendor/Camera-master/scripts/camera.min.js') ?> 

	    <!-- Mega menu  -->
	    <?= $this->Html->script('/vendor/bootstrap-mega-menu/js/menu.js') ?> 
		
		<!-- WOW js -->
		<?= $this->Html->script('/vendor/WOW-master/dist/wow.min.js') ?> 
		
		<!-- owl.carousel -->
		<?= $this->Html->script('/vendor/owl-carousel/owl.carousel.min.js') ?> 
		
		<!-- Fancybox -->
		<?= $this->Html->script('/vendor/fancybox/dist/jquery.fancybox.min.js') ?> 

		<!-- Theme js -->
		<?= $this->Html->script('/js/theme.js') ?> 

		<?= $this->fetch('script') ?>

		</div> <!-- /.main-page-wrapper -->
	</body>
</html>