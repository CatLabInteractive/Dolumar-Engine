<div>
	<?php if (count ($messages) > 0 && $showOlderMessages) { ?>
		<div class="older remove-after-insert-before">
			<a href="javascript:void(0);" onclick="windowAction(this, {'loadprevious' : <?=$messages[0]->getId ()?>});">
				<?=$this->getText ('oldermessages', 'chat', 'chat')?>
			</a>
		</div>
	<?php } ?>

	<?php foreach ($messages as $v) { ?>

		<div class="message">
			
			<p class="from"><?=$v->getPlayer ()->getDisplayName (); ?></p>
			<p class="date"><?=$v->getDisplayDate ();?></p>

			<?=Neuron_Core_Tools::output_text ($v->getMessage (), true, true, false, false); ?>

		</div>

	<?php } ?>
</div>