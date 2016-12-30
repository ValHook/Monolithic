<?php $this->assign('title', 'Connexion');?>
<?php $this->assign('titlePage', strtoupper(MONOLITHIC));?>
<?php $this->assign('noLogo', true); ?>


<div class="users form">
	
<?php echo $this->Flash->render('auth'); ?>

        <legend>
            <?php echo __('Connexion à '.MONOLITHIC); ?>
        </legend>
        <!-- SOCIAL -->
        <fieldset id="social-buttons">
	        
<?php
            echo $this->Html->link("<i class=\"fa fa-facebook\"></i>".__('Connexion avec Facebook'),
              "http://www.facebook.com/dialog/oauth?
            client_id=771486936314002&
            redirect_uri=http://www.monolithic.fr/joueurs/connexion&
            state=<?php echo $csrfToken; ?>&
            scope=email", array(
                "class" => 'btn  btn-secondary btn-fb btn-login-left',
                "escape" => false
            ));
        ?>

        </fieldset>

        <!-- DIVIDER -->
        <div class="row">
            <div class="col-xs-5 divider"></div>
            <div class="col-xs-2 col-center">OU</div>
            <div class="col-xs-5 divider"></div>
        </div>

        <!-- INPUTS -->
        <fieldset>
	<?php echo $this->Form->create('Player'); ?>
        <?php
            echo $this->Form->input('email', array("placeholder" => __("adresse@exemple.com")));
            echo $this->Form->input('password', array("placeholder" => __("Mot de passe")));
        ?>
    </fieldset>
	<?php echo $this->Form->input(__('Se connecter'), array("type" => "submit", "class" => "btn btn-pinky col-xs-2", 'label'=>false, 'div'=>false));
	echo $this->Form->end(); ?>
	
    <div class="col-xs-push-4 col-xs-6 text-right">
        <?php $alternativelinks = array($this->Html->link(__('Vous pouvez aussi vous inscrire'), REGISTER_LINK, array("class" => 'pinky')),
                                    $this->Html->link(__('Mot de passe oublié ?'), FORGOTTEN_PASSWORD_LINK, array("class" => 'pinky'))); 
        echo $this->Html->nestedList($alternativelinks); ?>
    </div>
    
</div>
