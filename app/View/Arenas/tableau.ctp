<?php
$this->assign('title', 'MiniMap');
$this->assign('titlePage', 'La carte');
$this->extend('action');
$this->start('tableau');?>
<div style="text-align:center; font-family: monospace">
<?php foreach($carte as $i): ?>
	<p class="retour">
	<?php foreach($i as $j): 
		if($j == 'H'): ?>
			<span class="green">H</span>
		<?php  
		elseif($j == 'X'): ?>
			<span class="red">X</span>
		<?php 
		else : echo($j); endif;?>
	<?php endforeach;?>
	</p>
<?php endforeach; ?>
</div>
<?php $this->end();?>