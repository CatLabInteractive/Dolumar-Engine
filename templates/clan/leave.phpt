<?=$this->setTextSection ('leave', 'clan');?>

<h2><?=Neuron_Core_Tools::putIntoText($this->getText ('leave'), array ('clan' => $clan))?></h2>

<?php if (!isset ($error)) { ?>
	<p class="true"><?=$this->getText ('done')?></p>
<?php } else { ?>
	<p class="false"><?=$this->getText ($error); ?></p>
<?php } ?>
