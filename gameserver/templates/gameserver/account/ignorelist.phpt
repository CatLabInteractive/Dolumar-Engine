<?=$this->setTextSection ('ignorelist', 'account'); ?>

<p><?=$this->getText ('about'); ?></p>

<h2><?=$this->getText ('ignore'); ?></h2>

<?php if (isset ($error)) { ?>
	<p class="false"><?=$this->getText ($error)?></p>
<?php } ?>

<form onsubmit="return submitForm(this);">
	<fieldset>
		<ul>
			<li>
				<label><?=$this->getText ('nickname'); ?></label>
				<input type="text" name="nickname" value="<?=$nickname?>" />
			</li>
			
			<li>
				<button type="submit" value="ignore"><?=$this->getText ('button');?></button>
			</li>
		</ul>
	</fieldset>
</form>

<h2><?=$this->getText ('ignoring'); ?></h2>
<?php if (isset ($list_players)) { ?>
	<ul>
		<?php foreach ($list_players as $v) { ?>
		<li>
				<?=$v['name']?> (<a href="javascript:void(0);" onclick="windowAction (this,{'unignore':<?=$v['id']?>});"><?=$this->getText ('unignore')?></a>)
			</li>
		<?php } ?>
	</ul>
<?php } else { ?>
	<p><?=$this->getText ('notIgnoring')?></p>
<?php } ?>
