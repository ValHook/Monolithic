<?php 
	$this->assign('title', 'Messages Privés');
	if(!isset($fighterPerso)): ?>
	<?php $this->assign('titlePage', 'Messages Privés') ?>
	<div class="row text-center">
		<?php
				echo $this->Form->create(NULL, array('id'=>'search_for', ));
				?>
			<div class="col-xs-10">
				<?php
				echo $this->Form->input('recherche', array("name" => "recherche",'class'=>"col-xs-10", 'label'=>false, "placeholder" => __("Entrer le nom du fighter")));?>
			</div>
			<div class="col-xs-1 size-loop clickable-loop" onClick="searchFighter()">
				<?php
				echo $this->Html->tag('i', '',array('class'=>'fa fa-search pink col-xs-1', 'id'=>'loopSearch'));?>
			</div>
		</form>
	</div>
	<?php if(isset($error)): ?>
		<div class="alert alert-danger">
		  <strong>Erreur!</strong>Le fighter <?php echo $errorName ?> n'existe pas
		</div>
	<?php endif; ?>
	<div class="row">
		<hr>
	</div>
	<?php if(isset($fighters)): ?>
		<?php foreach ($fighters as $fighter): ?>
		<div class="row text-center select-for-message" onClick="SelectForMessage('<?php echo ($fighter['Fighter']['name']); ?>')">
			<div class="col-xs-3">
				<?php if ($fighter['Fighter']['avatar_url'] != 'noPhoto') {?>
					<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . $fighter['Fighter']['avatar_url']); ?>" alt="fighter" class="img-responsive sender-avatar">
				<?php } else { ?>
					<img src="<?php echo (ROOT_FOLDER.'img/avatars/' . 'noPhoto.png'); ?>" alt="fighter" class="img-responsive sender-avatar">
				<?php } ?>
			</div>
			<div class="col-xs-3">
				<div class="name_journal1"> <?php echo ($fighter['Fighter']['name']); ?></div>
				<div class="level_journal1"> level : <?php echo ($fighter['Fighter']['level']); ?> </div>
			</div>
			<div class="col-xs-4 guilde-jOnLine">
				<div class="row">
					<?php  if ($fighter['Fighter']['guild_id'] != false) { ?>
					<div class="col-xs-5"> 
						<img src="<?php echo (ROOT_FOLDER.'img/guildeIcon.jpg'); ?>" alt="guilde" class="img-responsive guilde-icon">
					</div>
					<div class="col-xs-5 guildeName-jOnLine"> 
						<?php echo ($fighter['Fighter']['guild_name']); ?>
					</div>
					<?php } else { ?>
					<div class="col-xs-10 guildeName-jOnLine"> 
						No Guilde
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="col-xs-2">
				<div class="row">
					<i class="fa fa-chevron-right pink chevron-margin-top"></i>
				</div>
			</div>
		</div>
			<div class='row'>
				<hr>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endif; ?>
<?php if(isset($fighterPerso)): ?>
	<?php $this->assign('titlePage', $fighterPerso['0']['Fighter']['name']) ?>
	<div class="row margin-top-retour-chat">
		<?php echo $this->Html->link('<i class="fa fa-chevron-left"></i> Retour', '/arenas/messages', array('escape' => false, 'class' => 'pink')); ?>
	</div>
	<div class="row text-center">
		<div id="chat-messages-private" class='chat-messages-private'>
		</div>
		<?php
			echo $this->Form->create(NULL, array('id'=>'send-chat-message-private', 'class'=>"send-chat-message-private chat-messages-private margin-top-form-chat", 'data' => $fighterPerso['0']['Fighter']['id']));
			?>
		<div class="col-xs-11">
			<?php 
				echo $this->Form->input('text', array("name" => "message", 'label'=>false, 'div'=>false, "placeholder" => __("Entrer un message ici")));
			?>
		</div>
		<div = class="col-xs-1">
			<?php
				echo $this->Html->tag('i', '',array('class'=>'fa fa-check pink', 'id'=>'checkmarkprivate'));
				?>
		</div>
		</form>
	</div>
<?php endif; ?>