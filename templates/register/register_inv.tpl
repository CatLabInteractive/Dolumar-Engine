<p><?=$locked?></p>
<p class="false"><?=$invitation?></p>

<form onsubmit="submitForm(this); return false;">
	<fieldset>
		<legend><?=$this->getText('legend','locked','account');?></legend>
		<label><?=$code?>:</label>
		<input type="text" name="invCode" value="<?=$invCode?>" />
		<button type="submit"><?=$submit?></button>
	</fieldset>
</form>
