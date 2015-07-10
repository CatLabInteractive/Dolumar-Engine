<?php
	$input = Neuron_Core_Tools::getInput ('_POST', 'bbcode', 'varchar');
?>

<h2>Input</h2>
<form method="post">
	<fieldset>
		<ol>
			<li>
				<label>BBCode:</label>
				<textarea name="bbcode" cols="100" rows="20"><?=$input?></textarea>
			</li>
			
			<li>
				<button type="submit"><span>Submit</span></button>
			</li>
		</ol>
	</fieldset>
</form>

<h2>Output</h2>
<?php
	echo '<pre>'.htmlentities (Neuron_Core_Tools::output_text ($input)).'</pre>'; 
?>
