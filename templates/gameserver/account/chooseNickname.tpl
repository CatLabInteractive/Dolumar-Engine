<h2><?=$welcome?></h2>

<p>
	<?php
	
	$tos = $this->getText ('tos', 'tos', 'account');
	$pp = $this->getText ('pp', 'tos', 'account');
	
	echo Neuron_Core_Tools::putIntoText
	(
		$this->getText ('register', 'tos', 'account'),
		array
		(
			'pp' => '<a href="javascript:void(0);" onClick="openWindow(\'help\',{\'page\':\'Privacy_Policy\'});" title="'.$pp.'">'.$pp.'</a>',
			'tos' => '<a href="javascript:void(0);" onClick="openWindow(\'help\',{\'page\':\'TOS\'});" title="'.$tos.'" >'.$tos.'</a>'
		)
	);
	?>
</p>

<p><?=$chooseName?></p>

<?php
if (isset ($error))
{
	echo '<p class="false">'.$error.'</p>';
}
?>

<form onsubmit="return submitForm(this);">
	<fieldset>
		<label><?=$username?>:</label>
		<input name="username" type="text" style="width: 150px;" value="<?=$username_value?>" />
		
		<button type="submit" value="<?=$submit?>"><?=$submit?></button>
	</fieldset>
</form>
