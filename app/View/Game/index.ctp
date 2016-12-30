<?php
$this->assign('noBox','true');
$this->assign('title', 'Choisir Arene');
$this->assign('titlePage', 'Choisir Arene');
echo $this->Html->css('myfighter');
?>
<?php for($i = 0; $i < $numberOfArena; $i++): ?>
	<?php $lien = $numDeJoueur[$i] < MAX_PLAYERS_IN_ARENA ? "/game/arene_".$areneName[$i] : "#"; ?>
	<a class="content row character-content cover-<?php echo $areneName[$i]; ?>" href="<?php echo $lien; ?>">
		<div class="arena-overlay">
		<?php 
		if ($numDeJoueur[$i] < MAX_PLAYERS_IN_ARENA)
			echo (!$numDeJoueur[$i] ? "Aucun" : $numDeJoueur[$i]) ." joueur". ($numDeJoueur[$i] > 1 ? "s" : ""). " en ligne";
		else
			echo '<span class="pinky" style="text-decoration: none !important">Complet - '.$numDeJoueur[$i].' joueurs</span>';
		?>
		</div>
 	</a>
<?php endfor; ?>
