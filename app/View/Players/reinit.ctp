<?php $this->assign('title', 'Réinitialisation mot de passe');?>
<?php $this->assign('titlePage', 'Réinitialisation');?>


<div class="users form">
<?php
echo $this->Form->create('reinitialisation'); ?>
	<fieldset>
		<?php echo $this->Form->input('email', array('label' => __("Saisissez votre adresse email"),"placeholder" => __("adresse@exemple.com"))); ?>
		<?php echo $this->Form->input('code', array('label' => __("Saisissez le code reçu"),"placeholder" => __("code"))); ?>
		<?php echo $this->Form->input('password', array('label' => __("Saisissez votre nouveau mot de passe"),"placeholder" => __("Nouveau mot de passe"))); ?>
	</fieldset>
	<div class="row">
	<?php echo $this->Form->input(__('Envoyer le nouveau mot de passe'), array("type" => "submit", "class" => "btn btn-pinky col-xs-5", 'label'=>false)); ?>
	<?php echo $this->Form->end();?>
	<?php  echo $this->Html->link(__('Revenir à la connexion'), LOGIN_LINK, array('class' => 'pinky col-xs-7 text-right')); ?>
	</div>
</div>