<div class="chatcontainer">
	<div class="chatdiv" onclick="document.getElementById('chatdiv_input').focus();">
		<p class="false">
			<?=
				Neuron_Core_Tools::putIntoText
				(
					$this->getText ('rules', 'chat', 'chat'),
					array
					(
						'tos' => '<a href="javascript:void(0);" onclick="openWindow(\'help\',{\'page\':\'Chat_TOS\'});">'.
							$this->getText ('tos', 'chat', 'chat').'</a>'
					)
				)
			?>
		</p>
	</div>
	<div class="chatform">
		<form onsubmit="submitForm(this);document.getElementById('chatdiv_input').value='';document.getElementById('chatdiv_input').focus();return false;">
			<fieldset>
				<input onclick="this.focus();" maxlength="160" name="message" id="chatdiv_input" autocomplete="off" />
				<button type="submit"><?=$send?></button>
			</fieldset>
		</form>
	</div>
</div>
