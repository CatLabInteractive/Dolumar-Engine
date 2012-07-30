<?php $this->setTextSection ('Tutorial1', 'tutorial'); ?>

<p><?=$this->getText ('line1'); ?></p>
<p>
	<span class="navigation build icon" title="<?=$this->getText ('build', 'menu', 'main')?>">
		<span><?=$this->getText ('build', 'menu', 'main')?></span>
	</span>
	
	<?=$this->getText ('line2'); ?>
</p>
<p><?=$this->getText ('line3'); ?></p>
