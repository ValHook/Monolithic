<?php 
$this->assign('title', 'Guilde');
$i=0;
//NO GUILD
if ($noGuilde) {
$this->assign('titlePage', 'Guilde');
?>
<div class="row text-center">
	<div class="noGuilde-head ">  Vous n'avez pas encore de Guilde ? </div>
</div>

<div class="row text-center">
	<div class="col-xs-6">
		<?php echo $this->Html->tag('div', $this->Html->link(__('TROUVER'), '#', array('class' => 'button_guildeFC unSelect', 'onClick' => 'buttonFindGuildDidTuch();'))); ?>
	</div>
	<div class="col-xs-6">	
		<?php echo $this->Html->tag('div', $this->Html->link(__('CREER'), '#', array('class' => 'button_guildeFC Select select', 'onClick' => 'testestest();'))); ?>
	</div>
</div>

<div id="creatGuild">
	<div class="row text-center"> 
		<?php
			echo $this->Form->create('CreatGuilde');
			echo $this->Form->input('nom', array('placeholder' => __('Nom de la guilde'), 'class' => 'imputGuilde' ));
			echo $this->Form->input(__('Créer'), array("type" => "submit", 'class' => 'btn btnsubmitGuilde col-xs-2', 'label'=>false, 'div'=>false));
			echo $this->Form->end();
		?>
	</div>
</div>

<div id="findGuild">
	<?php while($i <= $number) {?>
		<div class="row">
			<div class="col-xs-2">
				<img src="<?php echo (ROOT_FOLDER.'img/' . 'guildeIcon.jpg'); ?>" class="guildeicon">
			</div>
			<div class="col-xs-2">
				<div class="guildname">
					<strong class='size-nom-guild'>
					<?php echo $guildeexist[$i]['Guild']['name']; ?>
					</strong>
				</div>
			</div>
			<div class="col-xs-4">
				<div class="nbrfighter">Fighters: <?php echo $guildeexist[$i]['Guild']['fighters'];?></div>
				<div class="nbrfighter">Average lvl: <?php echo $guildeexist[$i]['Guild']['levelmoy'];?></div>
			</div>
			<div class="col-xs-4">
				<?php /*echo $this->Html->tag('div', $this->Html->link('Rejoindre', '#', array('class' => 'btnjoinguild', 'escape' => false))); /*$this->HTML->link('<i class="fa fa-power-off"></i> Déconnexion', '#', array('escape' => false))*/ 
				echo $this->Form->create('Join');
				echo $this->Form->input(__('Rejoindre'), array("type" => "submit", 'class' => 'button_guildeFC Select', 'label'=>false));
				echo $this->Form->input('', array('value' => $guildeexist[$i]['Guild']['id'], 'class' => 'imputGuilde hidden' ));
				echo $this->Form->end();
				?>
			</div>
		</div>
		<div>
			<hr>
		</div>
	<?php $i++; } ?>
</div>

<?php 
//HAVE GUILD
} else {
	$this->assign('titlePage', $guildname);
	$i=0;
	while($i<$countinguild+1) { ?>
		<div class="contant-jOnLine row">
			<div class="col-xs-3">
					<?php if ($fighteringuild[$i]['Fighter']['avatar_url'] != 'noPhoto' && $fighteringuild[$i]['Fighter']['avatar_url'] != NULL ) {?>
						<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighteringuild[$i]['Fighter']['avatar_url']); ?>" alt="fighter" class="img-responsive sender-avatar">
					<?php } else { ?>
						<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . 'noPhoto.png'); ?>" alt="fighter" class="img-responsive sender-avatar">
					<?php } ?>
			</div>
			<div class="col-xs-3">
				<div class="name_journal1" style="overflow: initial"><?php echo($fighteringuild[$i]['Fighter']['name']); ?></div>
				<div class="level_journal1">Level : <?php echo $fighteringuild[$i]['Fighter']['level']; ?></div>
			</div>
		<div class="col-xs-2 guilde-jOnLine"></div>
		<div class="col-xs-4">
			<div class="middles">
			<?php echo $this->Html->tag('div', $this->Html->link(__('Message'), '#', array('class' => 'mono button_guildeFC Select select taille'))); ?>
			</div>
		</div>
	</div>
		<div class="row"> 
		<?php  if ($i != $countinguild) { ?>
			<hr>
		<?php } ?>
		</div>
<?php 

++$i;
}

?>
	</div>
		<div class="btnquit">
			<?php echo $this->Form->create('Leave');
			echo $this->Form->input(__('Quitter'), array("type" => "submit", 'class' => 'button_guildeFC Select select centrerbtn', 'label'=>false));
			echo $this->Form->input('', array('value' => $guildid, 'class' => 'imputGuilde hidden' ));
			echo $this->Form->end();?>
		</div>
	<?php } ?>
	