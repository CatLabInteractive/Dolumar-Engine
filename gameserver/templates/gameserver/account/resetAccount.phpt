<?php $this->setTextSection ('resetaccount', 'account'); ?>

<p><?=$this->getText ('about')?></p>

<?php if (isset ($noEmail)) { ?>
	<p class="false"><?=$this->getText ('emailcert')?></p>
<?php } else { ?>

	<?php if (isset ($success)) { ?>
		<p class="true"><?=$this->getText ($success)?></p>
	<?php } else { ?>
		<?php $toReset = $this->getClickTo ('confirm'); ?>
		<p>
			<?=$toReset[0]?><a href="javascript:void(0);" onclick="confirmAction(this,{'action':'resetAccount','confirm':'true'}, '<?=addslashes($this->getText ('yousure')); ?>')"><?=$toReset[1]?></a><?=$toReset[2]?>
		</p>
	<?php } ?>
<?php } ?>

<p>
	<a class="button" href="javascript:void(0);" onclick="windowAction (this, {'action':'home'});"><?=$this->getText ('back', 'changepass', 'account')?></a>
</p>
