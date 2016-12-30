<?php
 $this->assign('title', 'Joueurs en ligne');
 $this->assign('titlePage', 'Joueurs en ligne');

$total = count($fighters);
$i = 0;
while ($total > $i) {
 ?>
<div class="contant-jOnLine">
<div class="row text-center">
	<!-- F1-->
	<div class="col-xs-3">
		<?php if ($fighters[$i][2] != 'noPhoto') {?>
			<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighters[$i][2]); ?>" alt="fighter" class="img-responsive sender-avatar">
		<?php } else { ?>
			<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . 'noPhoto.png'); ?>" alt="fighter" class="img-responsive sender-avatar">
		<?php } ?>
	</div>
	<div class="col-xs-3">
		<div class="name_journal1"> <?php echo ($fighters[$i][0]); ?></div>
		<div class="level_journal1"> level : <?php echo ($fighters[$i][1]); ?> </div>
	</div>
	
	<div class="col-xs-4 guilde-jOnLine">
		<div class="row">
			<?php  if ($fighters[$i][3] != false) { ?>
			<div class="col-xs-5"> 
				<img src="<?php echo (ROOT_FOLDER.'img/guildeIcon.jpg'); ?>" alt="guilde" class="img-responsive guilde-icon">
			</div>
			<div class="col-xs-5 guildeName-jOnLine"> 
				<?php echo ($fighters[$i][3]); ?>
			</div>
			<?php } else { ?>
			<div class="col-xs-10 guildeName-jOnLine"> 
				No Guilde
			</div>
			<?php } ?>
		</div>
	</div>
	
	<div class="col-xs-2">
		<div class="row">
		<?php
			echo $this->Html->tag('div', $this->Html->link(__('Message'), '/arenas/messages', array('class' => 'messageb button_jLigne')));
		?>
		</div>
		<div class="row">
		<?php	
			echo $this->Html->tag('div', $this->Html->link(__('Rejoindre'), '/game/arene_'.$fighters[$i][4], array('class' => 'attaqueb button_jLigne'))); 
		?>
		</div>	
	</div>
</div>

<div class="row"> 
	<?php  if ($i != $total-1) { ?>
		<hr>
	<?php } ?>
</div>
</div>
<?php 

++$i;
}
if ($total == 0) { ?>
	<p style="width: 100%; text-align: center">Il n'y a aucun joueur en ligne</p>
<?php }
?>
