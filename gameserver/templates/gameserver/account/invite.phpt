<?php $this->setTextSection ('invite', 'account'); ?>

<p><?=$this->getText ('invite');?></p>
<p><?=$this->getText ('legend');?></p>

<form onsubmit="return false;">
	<fieldset>
		<label><?=$this->getText ('url')?>:</label>
		<input type="text" readonly="readonly" value="<?=$sUrl?>" onclick="this.focus();this.select()" />
	</fieldset>
</form>
