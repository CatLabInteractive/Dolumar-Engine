<?php if ($section == 'choosemail') { ?>

	<h2><?=$email_title?></h2>
	
	<?php if (isset ($error)) { ?>
		<p class="false"><?=$error?></p>
	<?php } ?>

	<p><?=$about?></p>	
	<form onsubmit="return submitForm (this);">

		<fieldset>
		
			<input type="hidden" class="hidden" name="formAction" value="email" />
			
			<label><?=$email?>:</label>
			<input type="text" name="email" />
			<button type="submit"><?=$submit?></button>
		</fieldset>

	</form>
<?php } elseif ($section == 'notcertified') { ?>

	<h2><?=$email_title?></h2>
	<p><?=$email_about?></p>
	
	<p>
		<?=$cert_again[0]?><a href="javascript:void(0);" onclick="windowAction(this,{'formAction':'email','action':'resend'});"><?=$cert_again[1]?></a><?=$cert_again[2]?>
	</p>

<?php } ?>

<p>
	<?=$return[0]?><a href="javascript:void(0);" onclick="windowAction(this,'formAction=overview');"><?=$return[1]?></a><?=$return[2]?>
</p>
