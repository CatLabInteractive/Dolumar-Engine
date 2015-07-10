<?php $this->setTextSection ('premium', 'account'); ?>

<?php if (isset ($error)) { ?>
	<p class="false"><?=$error?></p>
<?php } ?>

<?php if (isset ($notPremium1)) { ?>
	<p class="false">
		<?=$notPremium1?>
	</p>
<?php } else { ?>
	<p class="true">
		<?=$premiumLast?>
	</p>
<?php } ?>

<?php if (isset ($benefits)) { ?>
	<?=$benefits?>
<?php } ?>

<p class="maybe">
	<?=Neuron_Core_Tools::putIntoText
	(
		$this->getText ('isoptional'),
		array
		(
			'optional' => '<strong>'.$this->getText ('optional').'</strong>'
		)
	);?>
</p>

<p><?=$toUse[0]?><a href="<?=$extend_url?>" target="_BLANK" onclick="return !Game.gui.openWindow('<?=$extend_url?>', 450, 190);"><?=$toUse[1]?></a><?=$toUse[2]?></p>
