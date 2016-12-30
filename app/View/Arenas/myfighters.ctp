<?php
$this->assign('noBox','true');
$this->assign('title', 'Mes combattants');
$this->assign('titlePage', 'Selectionner un combattant');
echo $this->Html->css('myfighter');
$this->assign('noMenu','true');
?>
<?php if($fighters): ?>
	<?php foreach ($fighters as $fighter): ?>
		<?php if ($fighterId && $fighterId != $fighter['Fighter']['id']): ?>
			<div class="content row character-content transparent">
		<?php else : ?>
			<div class="content row character-content">
		<?php endif; ?>
			<div class="row" id="<?php echo($fighter['Fighter']['id'])?>" onClick="color(<?php echo($fighter['Fighter']['id'])?>)">
				<div class="col-xs-4">
					<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighter['Fighter']['avatar_url']); ?>" alt="fighter" class="img-responsive sender-avatar" style="width:85px; height:85px;">
					<div class="name"><?php echo $fighter['Fighter']['name']; ?></div>
				</div>
				<div class="col-xs-4" style="text-align: center">
					<div class="niveau">Niveau : <?php echo $fighter['Fighter']['level']; ?></div>
					<div class="barredxp">
						<div class="barre">
							<div id="xp" style="width:<?php echo (string)(100*$fighter['Fighter']['xp']/$fighter['Fighter']['level']) ?>%;"></div>
						</div>
					</div>
					<div class="espace"></div>
					<div>
						<img src="<?php echo (ROOT_FOLDER.'img/' . 'guildeIcon.jpg'); ?>" style="width:25%; border-radius:10px;">
						<div class="guilde"><?php echo $fighter['Fighter']['guild_id']; ?></div>
					</div>
				</div>
				<div class="col-xs-2">
					<div class="stat"> 
						<p><i class="fa fa-heart"></i>  <?php echo $fighter['Fighter']['skill_health']; ?></p>
						<p><?php echo $this->Html->image("icons/force2.png", array("width" => "20",  'height' => "20"))?>  <?php echo $fighter['Fighter']['skill_strength']; ?></p>
						<p><i class="fa fa-eye"></i>  <?php echo $fighter['Fighter']['skill_sight']; ?></p>
						<p><?php echo $this->Html->image("icons/speed2.png", array("width" => "20",  'height' => "20"))?>  <?php echo $fighter['Fighter']['skill_speed']; ?></p>
					</div>
				</div>
				<div class="col-xs-2">
					<div class="margindel">
					</div>
					<div>
						<?php 
							echo $this->Form->create('delete');
							echo $this->Form->input('X', array('type' => 'button', 'class' => 'button_guildeFC Select select', 'label'=>false, 'espape' => false, 'value'=> $fighter['Fighter']['id']));
							echo $this->Form->input('', array('value' => $fighter['Fighter']['id'], 'class' => 'imputGuilde hidden'));
							echo $this->Form->end();
						?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
<?php if(!$fighters): ?>
	    <div id="content" class="row">Vous ne disposez actuellement pas de combattant, cliquer sur le bouton + pour en créer un.</div>
<?php endif; ?>
		<div class="content row createperso"><?php echo $this->Html->link('Créer un combattant!', CREATFIGHTER, array('class' => 'creaperso', 'escape' => false)); ?></div>
		