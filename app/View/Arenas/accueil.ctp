<?php 
$this->assign('title', 'Accueil');
$this->assign('titlePage', 'Bienvenue sur WebArena'); 
$this->assign('noMenu','true');?>
<div class="centre">
	<p class="titre">
		Bienvenue combattant !
	</p>
	<p>Pour pouvoir jouer, Vous devez auparavant selectioner votre personnage-joueur. Pour cela, cliquez sur le lien en bas de la page. Vous pouvez changer de personnage-joueur à tout moment en cliquant sur "Selection personnage".</p>
	<p>Pour entrer dans l'arène, rendez vous dans l'onglet "Arena". Montrez aux autres qui est le plus fort !</p>
	<p>Votre combattant n'est pas assez fort ? Personnalisez le et équipez le dans l'onglet "Mon combattant".</p>
	<p>Le journal de guerre permet de voir les morts récents ayant eu lieu dans le jeu.</p>
	<p>Vous souhaitez partager votre expérience de jeux avec d'autres ? Les onglets "Guilde" et "Messagerie" sont fait pour vous. De plus, vous avez toujours accès à la messagerie globale en cliquant sur le bouton en haut à droite.</p>
	<?php echo $this->Html->link('Sélectionnez votre personnage.', MY_FIGHTER, array('class' => 'pinky', 'escape' => false)); ?>
</div>