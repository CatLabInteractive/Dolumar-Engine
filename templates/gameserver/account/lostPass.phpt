<?php $this->setTextSection ('lostPassword', 'account'); ?>

<p><?=$this->getText ('about')?>:</p>
<form onsubmit="return submitForm(this);">
	<fieldset>
		<label><?=$this->getText ('email')?></label>
		<input type="text" name="email" />
		<button type="submit" value="retrievePassword"><?=$this->getText ('submit')?></button>
	</fieldset>
</form>
