<?php 
$this->assign('title', 'Journal');
$this->assign('titlePage', 'Journal de guerre');

$total = count($fighters);
$i = 0;
// Cas ou il n'y a pas de journal
if ($total == 0) { ?>
	<div class="row text-center ">Vous n'avez pas encore fait parler votre puissance dans l'arène</div>
<?php } 
	
// Journal KD
else { ?>
<div class="row text-center ">
	<div class="col-xs-6 ">
		<div class="name_journal1" style="text-align: center; margin-top: -5px;">Ratio K/D Général</div>
		<div class="vs_journal" style="margin: 5px 0"><?php echo $kd['kda']; ?></div>
		<div class="level_journal2" style="text-align: center"><?php echo $kd['kall']; ?> frags, <?php echo $kd['dall']; ?> morts</div>
	</div>
	<div class="col-xs-6 ">
		<div class="name_journal1" style="text-align: center;  margin-top: -5px;">Ratio K/D Récent</div>
		<div class="vs_journal" style="margin: 5px 0"><?php echo $kd['kdr']; ?></div>
		<div class="level_journal2" style="text-align: center"><?php echo $kd['krecent']; ?> frags, <?php echo $kd['drecent']; ?> morts</div>
	</div>
</div>
<div class="row"> 
		<hr>
	</div>
<div class="row text-center ">
Historique de vos 20 derniers combats
</div>
<div class="row"> 
		<hr>
	</div>
<?php
// JOURNAL MATCHES
while ($total > $i) {
 ?>
<div class="row text-center ">
	
	<!-- F1-->
	<div class="col-xs-2 ">
		<?php if ($fighters[$i][4] != 'noPhoto') {?>
			<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighters[$i][4]); ?>" alt="fighter" class="img-responsive sender-avatar photo-journal">
		<?php } else { ?>
			<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . 'noPhoto.png'); ?>" alt="fighter" class="img-responsive sender-avatar photo-journal">
		<?php } ?>
	</div>
	<div class="col-xs-3">
		<div class="name_journal1"> <?php echo ($fighters[$i][0]); ?></div>
		<div class="level_journal1"> level : <?php echo ($fighters[$i][2]); ?> </div>
		<?php  if ($fighters[$i][7] == "F1_WON") { ?> 
			<div class="win_journal1">
				<i class="fa fa-trophy fa-2x "></i>
			</div>
		<?php } ?>
	</div>
	
	<div class="col-xs-2">
		<div class="vs_journal"> <?php echo $this->Session->read('current_fighter_name') == $fighters[$i][0] ? __('TUE') : __('MEURT') ; ?></div>
		<div class="date_journal"> <?php echo ($fighters[$i][6]); ?> </div>
	</div>
	
	<!-- F2-->
	<div class="col-xs-3">
		<div class="name_journal2"> <?php echo ($fighters[$i][1]); ?></div>
		<div class="level_journal2"> level : <?php echo ($fighters[$i][3]); ?> </div>
		<?php  if ($fighters[$i][7] == "F2_WON") { ?>
			<div class="win_journal2">
				<i class="fa fa-trophy fa-2x "></i>
			</div>
		<?php } ?>
	</div>
	<div class="col-xs-2">
		<?php if ($fighters[$i][5] != 'noPhoto') {?>
			<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighters[$i][5]); ?>" alt="fighter" class="img-responsive sender-avatar photo-journal">
		<?php } else { ?>
			<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . 'noPhoto.png'); ?>" alt="fighter" class="img-responsive sender-avatar photo-journal">
		<?php } ?>
	</div>
	
</div>
<?php  if ($i != $total-1) { ?>
	<div class="row"> 
		<hr >
	</div>
<?php } ?>
<?php 

++$i;
}
}
?>