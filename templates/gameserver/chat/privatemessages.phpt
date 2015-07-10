<?php $this->setTextSection ('messages', 'messages'); ?>

<?php include (TEMPLATE_DIR . 'blocks/pagelist.tpl'); ?>

<table class="tlist">

	<tr>
		<th>&nbsp;</th>
		<th class="message"><?=$this->getText ('message'); ?></th>
		<th class="date"><?=$this->getText ('date'); ?></th>
		<th class="player"><?=$this->getText ('from'); ?></th>
	</tr>

	<?php $alternate = true; ?>

	<?php if (count ($messages) == 0) { ?>
		<tr>
			<td colspan="4">
				<?=$this->getText ('nomessages'); ?>
			</td>
		</tr>
	<?php } ?>

	<?php foreach ($messages as  $v) { ?>

		<?php
			if ($alternate)
			{
				$alternate = false;
				$rowclass = "odd";
			}
			else
			{
				$alternate = true;
				$rowclass = "even";
			}
		?>

		<tr class="<?= $v->isRead () ? 'read' : 'unread' ?> <?=$rowclass?>">

			<td class="icon">
				<a href="javascript:void(0);" onclick="openWindow ('PrivateChat', {'id' : <?=$v->getPlayer ()->getId (); ?>});">
					<span class="icon message <?= $v->isRead () ? 'read' : 'unread' ?>" title="<?=$this->getText ('read'); ?>">
						<span><?=$this->getText ('read'); ?></span>
					</span>
				</a>
			</td>

			<td class="message">
				<a href="javascript:void(0);" onclick="openWindow ('PrivateChat', {'id' : <?=$v->getPlayer ()->getId (); ?>});"><?=Neuron_Core_Tools::output_varchar (trim ($v->getMessage (35)));?></a>
			</td>
			<td class="date"><?=$v->getDisplayDate (); ?></td>
			<td class="player">
				<a href="javascript:void(0);" onclick="openWindow ('PrivateChat', {'id' : <?=$v->getPlayer ()->getId (); ?>});">
					<?=Neuron_Core_Tools::output_varchar ($v->getPlayer ()->getName ());?>
				</a>
			</td>
		</tr>
	<?php } ?>
</table>

<?php include (TEMPLATE_DIR . 'blocks/pagelist.tpl'); ?>