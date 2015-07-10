<?php $this->setTextSection ('changepass', 'account'); ?>

<h2><?=$this->getText ('changePass')?></h2>

<?php if (isset ($error)) { ?>
	<p class="false"><?=$this->getText ($error)?></p>
<?php } ?>

<?php if (isset ($success)) { ?>
	<p class="true"><?=$this->getText ($success)?></p>
<?php } ?>

<form method="post" onsubmit="return submitForm (this);">
	<fieldset>
		<input type="hidden" class="hidden" name="action" value="changePassword" />
	
		<label><?=$this->getText ('pass1')?>:</label>
		<input type="password" name="newPassword1" />
		
		<label><?=$this->getText ('pass2')?>:</label>
		<input type="password" name="newPassword2" />
		
		<button type="submit"><?=$this->getText ('submit')?></button>
	</fieldset>
</form>

<p>
	<a class="button" href="javascript:void(0);" onclick="windowAction (this, {'action':'home'});"><?=$this->getText ('back')?></a>
</p>
