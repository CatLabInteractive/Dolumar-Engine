<?php $this->setTextSection ('vacationMode', 'account')?>

<?php if (isset ($success) && $success == true) { ?>
	<p class="true"><?=$this->getText ('disabled')?></p>
<?php } elseif (isset ($success)) { ?>
	<p class="false"><?=$this->getText ($error)?></p>
		
	<p><?=Neuron_Core_Tools::putIntoText ($this->getText ('active'), array ('since'=>$since))?></p>
	<p><a href="javascript:void(0);" onclick="windowAction(this,{'disable':'vacation'});"><?=$this->getText ('disable')?></a></p>
<?php } else { ?>

	<p><?=Neuron_Core_Tools::putIntoText ($this->getText ('active'), array ('since'=>$since))?></p>
	<p><a href="javascript:void(0);" onclick="windowAction(this,{'disable':'vacation'});"><?=$this->getText ('disable')?></a></p>

<?php } ?>
