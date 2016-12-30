<?php $this->assign('title', 'Inscription Joueur');?>
<?php $this->assign('titlePage', 'WEB ARENA');?>

<div class="users form">
<?php echo $this->Form->create('Player');?>
    <fieldset>
        <legend><?php echo __('Inscription Joueur'); ?></legend>
        <?php echo $this->Form->input('email', array("placeholder" => "adresse@exemple.com"));
              echo $this->Form->input('password', array("placeholder" => "Mot de passe"));
        ?>
    </fieldset>
<?php echo $this->Form->input(__('S\'inscrire'), array("type" => "submit", "class" => "btn btn-pinky col-xs-2", 'label'=>false, 'div'=>false));
echo $this->Form->end();
?>
<?php  echo $this->Html->link(__('Ou se connecter'), LOGIN_LINK, array('class' => 'col-xs-push-4 pinky col-xs-6 text-right')); ?>
</div>
