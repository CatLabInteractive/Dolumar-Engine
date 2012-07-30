<h2><?=$this->getText ('title')?></h2>
<p><?=$this->getText ('about')?></p>
<p><?=Neuron_Core_Tools::putIntoText ($this->getText ('distance'), array ('distance' => $distance))?></p>

<h2><?=$this->getText ('clans')?></h2>
<?php if (isset ($list_clans)) { ?>
	<ul>
		<?php foreach ($list_clans as $v) { ?>
			<li>
				<a href="javascript:void(0);" onclick="openWindow('clan', {'id':<?=$v['id']?>});"><?=$v['name']?></a>
			</li>
		<?php } ?>
	</ul>
<?php } else { ?>
	<p><?=$this->getText ('noclans');?></p>
<?php } ?>

<h2><?=$this->getText ('found');?></h2>
<?php if (isset ($error)) { ?>
	<p class="false"><?=$this->getText ($error)?></p>
<?php } ?>

<p><?=$this->getText ('aboutF')?></p>
<form onsubmit="return submitForm(this);">
	<fieldset class="simpleform">
		<legend><?=$this->getText ('details')?></legend>
		
		<ol>
			<li>
				<label><?=$this->getText ('name')?>:</label>
				<input type="text" name="clanname" />
			</li>
			
			<li>
				<button type="submit"><span><?=$this->getText ('submit')?></span></button>
			</li>
		</ol>
	</fieldset>
</form>
