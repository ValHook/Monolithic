<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset('utf-8'); ?>
	<title>
		<?php echo $this->fetch('title').TITLE_SUFFIX; ?>
	</title>
	<?php

		// Bootstrap
		echo $this->Html->css('bootstrap.min');
		// Font awesome
		echo $this->Html->css('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
		// Notre CSS pour ce layout
		echo $this->Html->css('home');
		echo $this->Html->css('common');

		echo $this->fetch('meta');
		echo $this->fetch('css'); 
	?>
		<link rel="icon" type="image/png" href="../img/favicon.ico" />
		

	</head>
<body>
<body>
	<div class="container">
		<header >
			<div class="row">
				<div class="col-xs-4">
 				<?php echo $this->Html->image('/img/logoWA.png', array('alt' => 'logo', 'id' => 'logo', 'class'=> 'img-responsive')); ?>
				</div>
				<div class="col-xs-8 text-right" id="slogan">
 					Le FPS totalement givrant, dans un navigateur internet!
 				</div>
			</div>
		</header>
	</div>
	<div class="container">
		<?php echo $this->fetch('content');?>
	</div>
	
	<p id="credits">Valentin Mercier • Edgar Georgel • Philippe Dupart • Victor Bernard</p>
	<footer>
		<div class="footer-green">
		</div>
		<div class="footer-blue">
		</div>
		<div class="footer-pink">
		</div>
	</footer>
	
</body>
</html>
