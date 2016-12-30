<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset('utf-8'); ?>
	<title>
		<?php echo $this->fetch('title').TITLE_SUFFIX; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// Bootstrap
		echo $this->Html->css('bootstrap.min');
		// Font awesome
		echo $this->Html->css('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
		// ionicons
		echo $this->Html->css('ionicons.min.css');
		// Notre CSS commun à tous les layouts
		echo $this->Html->css('common');
		// Notre CSS pour ce layout
		echo $this->Html->css('minimalist');

		echo $this->fetch('meta');
		echo $this->fetch('css'); ?>
		<link rel="icon" type="image/png" href="../img/favicon.ico" />
		

	</head>
<body>
	<!-- Fond flouté -->
	<div id="blur-backgroud"></div>

	<!-- MENU GAUCHE -->
	<?php if (AuthComponent::user('id') && !$this->fetch('noMenu')) { ?>
		<aside id="left-menu">
		<h2>Menu</h2>
		<nav>
			<?php
			$rf = substr(ROOT_FOLDER,0,-1);
			$list = array(
				$this->HTML->link('<i class="fa fa-optin-monster"></i>Arènes',GAME, array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-users"></i> Joueurs en ligne', '/arenas/onLineFighter', array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-pied-piper-alt"></i> Mon combattant', EDITFIGHTER, array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-check-square-o"></i> Sélection personnage',MY_FIGHTER, array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-newspaper-o"></i> Journal de guerre', '/arenas/diary', array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-trophy"></i> Guilde', '/arenas/guild', array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-comments-o"></i> Messages', '/arenas/messages', array('escape' => false)),
	            $this->HTML->link('<i class="fa fa-power-off"></i> Déconnexion', '/players/logout', array('escape' => false))
	        );
			echo $this->Html->nestedList($list);
			?>
		</nav>
		</aside>
	<?php } ?>



	<!-- DEBUT CORPS DE PAGE -->
	<main>
		<!-- EN TÊTE -->
		<header class="row">
			<!-- THREE BARS -->
			<div class="col-xs-2">
				<?php if (AuthComponent::user('id') && !$this->fetch('noMenu')) { ?>
				<i class="fa fa-bars fa-4x pink go-left"></i>
				<i class="fa fa-close fa-2x pink close-left"></i>
				<?php } ?>
			</div>
			<!-- LOGO -->
			<div id="logo" class="col-xs-8">
				<?php
					if (!$this->fetch('noLogo'))
						echo $this->Html->image('/img/logoWA.png', array('alt' => 'logo WA', 'class'=> 'img-responsive'));
					else {
						echo '<h1 class="top">';
						$caption = explode(' ',__($this->fetch('titlePage')));
						$caption_first_word = array_shift($caption);
						$caption_other_words = implode(' ', $caption);
						echo '<span class="pink">'.$caption_first_word.'</span> '.$caption_other_words;
						echo '</h1>';
					}
				?>
			</div>
			<!-- CHAT -->
			<div class="col-xs-2">
				<?php if (AuthComponent::user('id') && !$this->fetch('noMenu')) { ?>
				<i class="fa fa-close fa-2x pink close-right"></i>
				<i class="fa fa-comment-o fa-4x pink go-right"></i>
				<?php } ?>
			</div>
		</header>


		<!-- Titre -->
		<?php if (!$this->fetch('noLogo') || $this->fetch('edit')) { ?>
		<h1 class="col-xs-12"> 
			<?php if($this->fetch('img')){ ?>
				<?php if ($fighterStats[0]['Fighter']['avatar_url'] != 'noPhoto') {?>
					<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighterStats[0]['Fighter']['avatar_url']); ?>" alt="fighter" class="img-responsive sender-avatar sender-avatar-size">
				<?php } else { ?>
					<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . 'noPhoto.png'); ?>" alt="fighter" class="img-responsive sender-avatar sender-avatar-size">
				<?php } ?>
				
				<div class="uploadFlile">
				<div class="row">
					<div class="col-md-offset-5 col-md-2 ">
						
						<div id= 'upload-file-container'>
							<?php 
								echo $this->Form->create('FighterChooseAvatar', array('type'=>'file'));
								echo $this->Form->input('avatar_file',array('label' => '<i class="fa fa-upload fa-3x" style="text-shadow: 2px 2px #000;position:relative;z-index:999; cursor: pointer">  </i> ','escape' => false , 'type' => 'file'));
							?>
						</div>
						<div class= 'btn-upload' style="position: relative; left: -5px">
						<?php
							echo $this->Form->input('Sauvegarder', array("type" => "submit", "class" => "btn btn-pinky btn-xs", "label"=>false));
							echo $this->Form->end();
						?>
						</div>
					</div>
					
				</div>
				</div>

			<?php } ?>
			
						
			<?php if(!$this->fetch('img')){ ?>
			<?php $caption = explode(' ',__($this->fetch('titlePage')));
			$caption_first_word = array_shift($caption);
			$caption_other_words = implode(' ', $caption);
			echo '<span class="pink">'.$caption_first_word.'</span> '.$caption_other_words; ?>
			<?php } ?>
		</h1>
		<?php } ?>

		<!-- Boîte blanche -->
		<div class="container">
		<?php if (!$this->fetch('noBox')) { ?>
			<div class="row" id="content">
				<?php } ?>
				<?php echo $this->Flash->render(); ?>
				<?php echo $this->fetch('content'); ?>
				<?php if (!$this->fetch('noBox')) { ?>
			</div>
		<?php } ?>
		</div>

		<!-- Footer -->
		<footer>
			<p> Edgar Georgel • Valentin Mercier • Philippe Dupart • Victor Bernard </p>
		</footer>
	</main>
	<!-- FIN CORPS DE PAGE -->



	<!-- MENU DROITE -->
	<?php if (AuthComponent::user('id') && !$this->fetch('noMenu')) { ?>
		<aside id="right-menu">
			<div id="chat-tabs" class="row">
				<?php
					echo $this->Html->tag('div', $this->Html->link(__('Général'), '#', array('class' => 'active chat-tab chat-button-all', 'onClick' => 'chatSelectAll()')), array('class' => 'col-xs-5'));
					echo $this->Html->tag('div', $this->Html->tag('i','',array('class' => 'fa fa-comments fa-3x pink')), array('class' => 'col-xs-2'));
					echo $this->Html->tag('div', $this->Html->link(__('Guilde'), '#', array('class' => 'chat-tab chat-button-guild', 'onClick' => 'chatSelectGuild()')), array('class' => 'col-xs-5'));
				?>
			</div>

			<div id="chat-messages-all" class='chat-messages-all'>
			</div>
			<?php
				echo $this->Form->create(NULL, array('id'=>'send-chat-message-all', 'class'=>"send-chat-message-all chat-messages-all"));
				echo $this->Form->input('text', array("name" => "message", 'label'=>false, 'div'=>false, "placeholder" => __("Entrer un message ici")));
				echo $this->Html->tag('i', '',array('class'=>'fa fa-check pink', 'id'=>'checkmarkAll'));?>
			</form>
			<div id="chat-messages-guild" class='chat-messages-guild'>
			</div>
			<?php
				echo $this->Form->create(NULL, array('id'=>'send-chat-message-guild', 'class'=>"send-chat-message-all chat-messages-guild"));
				echo $this->Form->input('text', array("name" => "message", 'label'=>false, 'div'=>false, "placeholder" => __("Entrer un message ici")));
				echo $this->Html->tag('i', '',array('class'=>'fa fa-check pink', 'id'=>'checkmarkGuild'));
			?>
			</form>
		</aside>
	<?php } ?>


<script>
var root_folder = "<?php echo ROOT_FOLDER ?>";
var fighter_name = "<?php echo $this->Session->read('current_fighter_name'); ?>";
</script>
<?php
	echo $this->Html->script('jquery-2.1.4.min.js');
	echo $this->Html->script('bootstrap.min');
	echo $this->Html->script('navigation.js');
	if (AuthComponent::user('id')  && !$this->fetch('noMenu')) {
		echo $this->Html->script('chat.js');
	}
	echo $this->fetch('script');
	echo $this->Html->script('jquery-ui.min.js');
	echo $this->Html->script('edit.js');
?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-70662996-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
