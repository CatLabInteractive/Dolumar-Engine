<?php $this->setTextSection ('Tutorial2', 'tutorial'); ?>

<p><?=$this->getText ('line1'); ?></p>

<p>
	<span class="rune water icon" title="<?=$this->getText ('water', 'runeDouble', 'main')?>">
		<span><?=$this->getText ('water', 'runeDouble', 'main')?></span>
	</span>
	
	<span class="rune fire icon" title="<?=$this->getText ('fire', 'runeDouble', 'main')?>">
		<span><?=$this->getText ('fire', 'runeDouble', 'main')?></span>
	</span>
	
	<span class="rune earth icon" title="<?=$this->getText ('earth', 'runeDouble', 'main')?>">
		<span><?=$this->getText ('earth', 'runeDouble', 'main')?></span>
	</span>
	
	<span class="rune wind icon" title="<?=$this->getText ('wind', 'runeDouble', 'main')?>">
		<span><?=$this->getText ('wind', 'runeDouble', 'main')?></span>
	</span>
</p>

<p><?=$this->getText ('line2'); ?></p>
<p><?=$this->getText ('line3'); ?></p>
