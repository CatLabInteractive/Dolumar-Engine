<?php $this->setTextSection ('clanPassword', 'account'); ?>

<p><?=$this->getText ('about')?></p>

<?php if ($wrongPass) { ?>
	<p class="false"><?=$this->getText ('wrongPass')?></p>
<?php } ?>

<form onsubmit="return submitForm (this);">
	<fieldset>	
		<legend><?=Neuron_Core_Tools::putIntoText ($this->getText ('joinClan'), array ('clan' => $clanname))?></legend>
		
		<input type="hidden" class="hidden" name="clan" value="<?=$clan?>" />
		<input type="hidden" class="hidden" name="race" value="<?=$race?>" />
		
		<label><?=$this->getText ('password')?></label>
		<input type="password" name="password" />
		<button type="submit"><?=$this->getText ('submit')?></button>
	</fieldset>
</form>

<p>
	<a href="javascript:void(0);" onclick="windowAction (this, {'view':'home'});" class="button"><?=$this->getText ('back')?></a>
</p>
