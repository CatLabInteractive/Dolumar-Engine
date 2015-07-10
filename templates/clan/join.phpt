<h2><?=Neuron_Core_Tools::putIntoText($this->getText ('join'), array ('clan' => $clan))?></h2>

<?php if (isset ($error)) { ?>
	<p class="false">
		<?=
		$this->putIntoText 
		(
			$this->getText ($error),
			array
			(
				'own_clanhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_OWN_CLANHOP,
				'clanhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_CLANHOP,
				'joinhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_JOINS,
				'kicked' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_KICKJOIN
			)
		)
	?></p>
<?php } ?>

<?php if ($isProtected) { ?>
	<p><?=$this->getText('protected')?></p>
	<form onsubmit="return submitForm(this);">
		<fieldset>
			<legend><?=$this->getText ('label')?></legend>
			<label><?=$this->getText ('password')?></label>
			<input type="text" name="password" />
			<button type="submit"><?=$this->getText ('submit')?></button>
			
			<!-- Hidden action field -->
			<input type="hidden" class="hidden" name="action" value="join" />
			<input type="hidden" class="hidden" name="confirm" value="join" />
		</fieldset>
	</form>
<?php } else { ?>
	<p><?=$this->getText ('about')?></p>
	<?php $toJoin = $this->getClickTo ('toJoin'); ?>
	<p><?=$toJoin[0]?><a href="javascript:void(0);" onclick="windowAction(this,{'action':'join','confirm':'join'});"><?=$toJoin[1]?></a><?=$toJoin[2]?></p>
<?php } ?>
