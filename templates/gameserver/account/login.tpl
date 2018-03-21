<p><strong><?=$welcome?></strong></p>
<p><?=$login?></p>

<form onsubmit="return submitForm(this);">
	<fieldset>
	
		<legend><?=$login_title?></legend>
	
		<?php
			if (isset ($error))
			{
				echo '<p class="false">'.$error.'</p>';
			}
		?>

		<label><?=$username?>:</label>
		<input name="username" type="text" style="width: 150px;" />

		<label><?=$password?>:</label>
		<input name="password" type="password" style="width: 150px;" />
		
		<button type="submit"><?=$submit?></button>
	</fieldset>
</form>

<p>
	<?php echo $register[0].'<a href="javascript:void(0);" onclick="openWindow(\'register\', \'\');">'.$register[1].'</a>'.$register[2]; ?> <br />
	<?php echo $request[0].'<a href="javascript:void(0);" onclick="openWindow(\'lostpassword\',{});">'.$request[1].'</a>'.$request[2]; ?>
</p>

<p class="openid">
	<a href="javascript:void(0);" onclick="windowAction (this, {'action':'openid'});"><?=$this->getText ('openid')?></a>
</p>

<p class="openid">
	<a href="<?=ABSOLUTE_URL?>openid/login/?openid_url=<?=urlencode ('https://id.catlab.eu');?>"><?=$this->getText ('gambic')?></a>
</p>
