<?php 
$this->assign('titlePage', $fighterStats['0']['Fighter']['name'] . " Niveau " . $fighterStats[0]['Fighter']['level']);
echo $this->Html->css('edit');
$this->assign('noBox','true');
$this->assign('noLogo','true');
$this->assign('img','true');
$this->assign('edit','true');
$this->assign('title', 'Vestiaire');
?>



<div id="inventory" class="col-xs-2  content1 left-margin alpha-lower">
	<ul class="size-height-max">
		<?php foreach ($stuffInventory as $item): ?>
			<li id="<?php echo $item['tools']['id']?>" class='items' data="<?php echo $item['tools']['placement']?>">
							<!-- HTML to write -->
				<?php echo $this->Html->image("equipements/" . $item['tools']['icon_url'], array('alt' => $item['tools']['description'], 'class' => 'item-img'))?>
				<figcaption>
						<p><i class="fa fa-heart contain-figcaption"></i> : <?php echo $item['tools']['health'] ?></p>
						<p><?php echo $this->Html->image("icons/force.png", array("class" => "icon-img"))?> : <?php echo $item['tools']['strength'] ?></p>
						<p><i class="fa fa-eye"></i> : <?php echo $item['tools']['sight'] ?></p>
						<p><?php echo $this->Html->image("icons/speed.png", array("class" => "icon-img"))?> : <?php echo $item['tools']['speed'] ?></p>
						<p class="item-description"><?php echo $item['tools']['description_v'] ?></p>
				</figcaption>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<div id="cart" class="test col-xs-5 content1 col-xs-offset-1">
	<div class="alert alert-success alert-dismissible" hidden> Votre sauvegarde a bien été effectuée </div>
	<div class="alert alert-danger" hidden> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>  Votre item a été supprimé </div>
	
	<div class="stuff-presentation">
	<div class="row">
	<?php foreach ($itemSlot as $slot):?>
	<div class="col-md-6 col-center-block">
		<div class="ui-widget-content ui-widget-content-center ">
			<?php if ($slot == 'armor') { ?> <p class="police-prese"> Armure </p> <?php } else { ?> <p class="police-prese"> Arme de poing </p> <?php } ?> 
			<ul class="stuff-equipped equipped-item" data= "<?php echo $slot?>">
				<?php if(isset($fighterStuff[$slot])): ?>
					<li id="<?php echo $fighterStuff[$slot]['id']?>" class='items' data="<?php echo $fighterStuff[$slot]['placement']?>">
						<?php echo $this->Html->image("equipements/" . $fighterStuff[$slot]['icon_url'], array('alt' => $fighterStuff[$slot]['description'], 'class' => 'item-img')) ?>
						<figcaption >
								<p><i class="fa fa-heart contain-figcaption"></i> : <?php echo $fighterStuff[$slot]['health'] ?></p>
								<p><?php echo $this->Html->image("icons/force.png", array("class" => "icon-img"))?> : <?php echo $fighterStuff[$slot]['strength'] ?></p>
								<p><i class="fa fa-eye"></i> : <?php echo $fighterStuff[$slot]['sight'] ?></p>
								<p><?php echo $this->Html->image("icons/speed.png", array("class" => "icon-img"))?> : <?php echo $fighterStuff[$slot]['speed'] ?></p>
								<p class="item-description"><?php echo $fighterStuff[$slot]['description_v'] ?></p>
						</figcaption>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<?php endforeach; ?>
	</div>
	</div>
	
	<div class="row text-center">
		<!--<div class="col-md-6">
			<div class="ui-widget-content col-center-block">
				<ul class="fa fa-trash fa-3x" data = "remove">
				</ul>
			</div>
		</div>-->
		<div class="col-md-6 " >
			<button class="chat-tab" onClick='saveInventory()'><?php echo __("Sauvegarder"); ?></button>
		</div>
		<div class="col-md-6 " >
			<?php echo $this->Html->link('<button class="chat-tab chat-tab-red">'.__("Jouer").'</button>', GAME, array("escape"=>false)); ?>
		</div>
	</div>
		
</div>

<div class="col-xs-2 col-xs-offset-1 content1 alpha-lower">
	<div class="row center-element ">
		<p class="police-prese center-vertical"> <i class="fa fa-heart "></i> Vie </p>
	</div>
	<div class ="row text-center up">
		<div class="col-xs-8 ">
			<p id="skill-health" class="inline" data="health"><?php echo $fightersStatsEquiped['health']?> (+<?php echo $fightersStatsEquiped['health_item']?>)</p>
		</div>
		<div class="col-xs-2">
<!-- 		<button class="addskill" onclick="upSkill('skill_health')"><i class="fa fa-plus addskill-arow"></i></button> -->
			<i class=" addskill-arow fa fa-plus" onclick="upSkill('skill_health')"></i>
		</div>
	</div>
	<hr class="style-hr">
	<div class="row center-element ">
		<p class="police-prese"> <?php echo $this->Html->image("icons/force2.png", array("class" => "icon-img-skill"))?> Force</p>
	</div>
	<div class ="row center-element up">
		<div class="col-xs-8 ">
		<p id="skill-strength" class="inline" data="strength"><?php echo $fightersStatsEquiped['strength']?> (+<?php echo $fightersStatsEquiped['strength_item']?>)</p>
		</div>
		<div class="col-xs-2">
<!-- 		<button class="addskill" onclick="upSkill('skill_strength')"><i class="fa fa-plus color-white"></i></button> -->
			<i class=" addskill-arow fa fa-plus" onclick="upSkill('skill_strength')"></i>
		</div>
	</div>
	<hr class="style-hr">
	<div class="row center-element">
		<p class="police-prese"> <i class="fa fa-eye"></i> Portée </p>
	</div>
	<div class ="row center-element up">
		<div class="col-xs-8 ">
			<p id="skill-sight" class="inline" data="sight"><?php echo $fightersStatsEquiped['sight']?> (+<?php echo $fightersStatsEquiped['sight_item']?>)</p>
		</div>
		<div class="col-xs-2">
<!-- 		<button class="addskill" onclick="upSkill('skill_sight')"><i class="fa fa-plus color-white"></i></button> -->
			<i class=" addskill-arow fa fa-plus" onclick="upSkill('skill_sight')"></i>
		</div>
	</div>
	<hr class="style-hr">
	<div class="row center-element">
		<p class="police-prese"> <?php echo $this->Html->image("icons/speed2.png", array("class" => "icon-img-skill"))?> Vitesse </p>
	</div>
	<div class ="row center-element up">
		<div class="col-xs-8 ">
		<p id="skill-speed" class="inline" data="speed"><?php echo $fightersStatsEquiped['speed']?> (+<?php echo $fightersStatsEquiped['speed_item']?>)</p>
		</div>
		<div class="col-xs-2">
<!-- 		<button class="addskill" onclick="upSkill('skill_speed')"><i class="fa fa-plus color-white"></i></button> -->
			<i class=" addskill-arow fa fa-plus" onclick="upSkill('skill_speed')"></i>
		</div>
	</div>
	<hr class="style-hr">
	<div class="row center-element">
		<p class="police-prese">Points Restants</p>
	</div>
	<div class="row center-element up">
		<?php if ($fightersStatsEquiped['skill_point'] >= 0 ) { ?>
			<p id="skill-point" class="inline green-color"> <?php echo $fightersStatsEquiped['skill_point']?></p>
		<?php } else { ?>
			<p id="skill-point " class="inline red-color"> <?php echo $fightersStatsEquiped['skill_point']?></p>
		<?php } ?>
	</div>
</div>
