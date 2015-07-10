<?php $this->setTextSection ('chat', 'chat'); ?>

<div class="header">
	<?=$header?>
</div>

<div class="chat-container" onclick="document.getElementById('<?=$templateID?>_input').focus();">

	<div class="messagecontainer">
		<?=$messages?>
	</div>

	<div class="send">
		
		<form method="post" onsubmit="submitForm(this);document.getElementById('<?=$templateID?>_input').value='';document.getElementById('<?=$templateID?>_input').focus();return false;">

			<fieldset>
				<ol>
					<li class="input">
						<input onclick="this.focus();" maxlength="1000" name="message" id="<?=$templateID?>_input" autocomplete="off" />
					</li>

					<li class="button">
						<button type="submit"><?=$this->getText ('send'); ?></button>
					</li>
				</ol>
			</fieldset>

		</form>

	</div>
</div>