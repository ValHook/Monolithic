<?php $this->assign('title', 'Création du Personnage');?>
<?php $this->assign('titlePage', 'Création du Personnage');?>
<?php $this->assign('noMenu','true'); ?>


<?php if(isset($erreur)): ?>
	<div class="alert alert-danger">
	  <strong>Erreur</strong> Le nom indiqué a déjà été pris
	</div>
<?php endif; ?>
<div class="users form">
<?php
echo $this->Form->create('creat'); ?>
	<fieldset><?php
		echo $this->Form->input('nom', array('placeholder' => __('Nom du personnage'))); ?>
	</fieldset>
	<div class="row">
	<?php echo $this->Form->input(__('Créer le nouveau personnage'), array("type" => "submit", "class" => "btn btn-pinky col-xs-5", 'label'=>false)); ?>
<?php echo $this->Form->end();?>

	<?php  echo $this->Html->link(__('Revenir à la page des personnages'), MY_FIGHTER, array('class' => 'pinky col-xs-7 text-right')); ?>
	</div>

</div>