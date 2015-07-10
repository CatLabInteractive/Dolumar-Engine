<?php $this->setTextSection ('clanlogs', 'clan'); ?>

<p class="maybe"><?=$this->getText ('about'); ?></p>

<?php include (TEMPLATE_DIR.'blocks/pagelist.tpl'); ?>

<?php if (isset ($list_logs)) { ?>
	<table style="margin-top: 5px;">
		<?php foreach ($list_logs as $v) { ?>
			<tr>
				<td class="date"><?=$v['date']?></td>
				<td><?=$v['text']?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>

<?php $pagelist_loc = 'bottom'; include (TEMPLATE_DIR.'blocks/pagelist.tpl'); ?>
