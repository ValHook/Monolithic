<?php $this->assign('title', 'Mot de passe oublié');?>
<?php $this->assign('titlePage', 'WEB ARENA');?>


<div class="users form">
<?php
echo $this->Form->create('Oubli'); ?>
	<fieldset>
		<?php echo $this->Form->input('email', array('label' => __("Saisissez votre adresse email"),"placeholder" => __("adresse@exemple.com"))); ?>
	</fieldset>
	<div class="row">
	<?php echo $this->Form->input(__('Envoyer le nouveau mot de passe'), array("type" => "submit", "class" => "btn btn-pinky col-xs-5", 'label'=>false)); ?>
<?php echo $this->Form->end();?>

	<?php  echo $this->Html->link(__('Revenir à la connexion'), LOGIN_LINK, array('class' => 'pinky col-xs-7 text-right')); ?>
	</div>

</div>
