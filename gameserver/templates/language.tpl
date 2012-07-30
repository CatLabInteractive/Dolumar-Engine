<form onsubmit="return submitForm (this);">
	<fieldset>
		<legend><?=$language?></legend>
	
		<ol>
			<?php foreach ($list_languages as $v) { ?>
				<li>
					<input 
						<?php if ($v[0] == $current_language) { echo 'checked="checked"'; } ?>
						type="radio" 
						id="language<?= $v[0]?>" 
						name="language" 
						title="<?= $v[1]?>" 
						value="<?=$v[0]?>" 
						class="checkbox"
					/>
			
					<label for="language<?=$v[0]?>" class="checkbox">
						<img src="static/images/flags/<?=$v[0]?>.gif" /> <?=$v[1]?>
					</label>
				</li>
			<?php } ?>
			
			<li>
				<button type="submit" value="<?=$submit?>"><?=$submit?></button>
			</li>
		</ol>
	</fieldset>
</form>
