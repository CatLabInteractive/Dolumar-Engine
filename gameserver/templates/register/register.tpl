<?php if (isset ($error)) { echo '<p class="false">'.$error.'</p>'; } ?>

<p><?=$this->getText ('about')?></p>

<form onsubmit="submitForm(this);return false;">

	<fieldset>
		<legend><?=$register?></legend>
	
		<input type="hidden" name="invCode" value="<?=$invCode?>" class="hidden" />

		<ol>
			<li>
				<label><?=$username?>:</label>
				<input type="text" name="username" <?php if (isset ($username_value)) { echo 'value="'.$username_value.'"'; } ?> />
			</li>

			<li>
				<label><?=$email?>:</label>
				<input type="text" name="email" <?php if (isset ($email_value)) { echo 'value="'.$email_value.'"'; } ?> />
			</li>

			<li>
				<label><?=$password?>:</label>
				<input name="password" type="password" />
			</li>

			<li>
				<label><?=$password2?>:</label>
				<input name="password2" type="password" />
			</li>
		
			<?php
				$tos = Neuron_Core_Tools::putIntoText
				(
					$this->getText ('accept'),
					array
					(
						'terms' => '<a href="javascript:void(0);" onclick="openWindow(\'help\', {\'page\':\'TOS\'});">'.$this->getText ('terms').'</a>'
					)
				);
			?>
		
			<li>
				<input type="checkbox" class="checkbox" name="tos" />
				<label class="checkbox"><?=$tos?></label>
			</li>

			<li>
				<div class="buttons">
					<button type="submit" name="submit"><?=$submit?></button>
				</div>
			</li>
		</ol>
	</fieldset>
</form>
