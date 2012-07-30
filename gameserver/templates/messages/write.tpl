<h2><?=$write?></h2>
<?php if (isset ($sent)) { ?>
	<p class="true"><?=$sent?></p>
<?php } else { ?>
	<?php if (isset ($error)) { ?>
		<p class="false"><?=$error?></p>
	<?php } ?>

	<form onsubmit="return submitForm(this);">
		<fieldset>
			<input type="hidden" class="hidden" name="formAction" value="write" />
		
			<legend><?=$yourMessage?></legend>
		
			<label><?=$to?>:</label>
			<input type="input" name="target" value="<?php if(isset($to_value)){ echo $to_value; }?>" />
		
			<label><?=$subject?>:</label>
			<input type="text" name="subject" value="<?php if(isset($subject_value)){ echo $subject_value; }?>" />
		
			<label><?=$message?>:</label>
			<textarea name="message" cols="50" rows="5"><?php if(isset($text_value)){ echo $text_value; }?> </textarea>
		
			<button type="submit"><span><?=$sendMessage?></span></button>
		</fieldset>
	</form>
<?php } ?>
<p>
	<?=$toInboxUrl?>
</p>
