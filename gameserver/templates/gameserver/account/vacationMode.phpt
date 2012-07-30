<?php $this->setTextSection ('vacationMode', 'account'); ?>

<?php if (isset ($error)) { ?>
	<p class="false"><?=$this->getText ($error); ?></p>
<?php } ?>

<?php if (!$done) { ?>
	<p><?=$this->getText ('about')?></p>
	<p><?=$this->getText ('rules')?></p>
	
	<p><a href="javascript:void(0);" onclick="confirmAction(this, {'confirm':'<?=$checkkey?>'},'<?=$this->getText ('confirm')?>');"><?=$this->getText ('activate')?></a></p>
<?php } else { ?>
	<p class="true"><?=$this->getText ('done')?></p>
<?php } ?>
