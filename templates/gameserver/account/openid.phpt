<h2><?=$this->getText ('title')?></h2>
<p><?=$this->getText ('about')?></p>

<?php if (isset ($list_popular)) { ?>
	<h3><?=$this->getText ('popular')?></h3>
	<ul>
		<?php foreach ($list_popular as $v) { ?>
			<li><a href="<?=$url?>?openid_url=<?=urlencode ($v[1])?>"><?=$v[0]?></a></li>
		<?php } ?>
	</ul>
<?php } ?>

<h3><?=$this->getText ('specify')?></h3>
<form method="get" action="<?=$url?>">
	<fieldset class="simpleform">
		<legend><?=$this->getText ('legend')?></legend>
		
		<ol>
			<li>
				<label><?=$this->getText ('url')?>:</label>
				<input name="openid_url" type="text" class="openid" id="openidselector" />
			</li>
			
			<li>
				<div class="buttons">
					<button type="submit"><?=$this->getText ('submit')?></button>		
				</div>
			</li>
		</ol>
	</fieldset>
</form>

<p>
	<a href="http://www.openid.net/" target="_BLANK"><?=$this->getText ('whatIs')?></a>
</p>

<?php $return = $this->getClickTo ('toReturn'); ?>

<p>
	<?=$return[0]?><a href="javascript:void(0);" onclick="windowAction(this,{'action':'overview'});"><?=$return[1]?></a><?=$return[2]?>
</p>
