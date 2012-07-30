<p style="float: right;">
	<?=$toOutboxUrl?>
</p>

<h2><?=$inbox?></h2>
<?php if (isset ($list_messages)) { ?>

	<?php include (TEMPLATE_DIR . 'blocks/pagelist.tpl'); ?>

	<table class="tlist">
	
		<tr>
			<th>&nbsp;</th>
			<th><?=$subject?></th>
			<th style="text-align: center;"><?=$from?></th>
			<th class="date"><?=$date?></th>
			<th>&nbsp;</th>
		</tr>
	
		<?php $alternate = true; ?>
		<?php foreach ($list_messages as $v) { ?>		
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
		
		<tr class="<?=$v['isRead'] ? 'read' : 'unread'?> <?=$rowclass?>">
			<td class="icon"><?=$v['sReadUrl']?></td>
		
			<td style="width: 40%;">
				<?=$v['sUrl']?>
			</td>
			<td style="text-align: center;">
				<!-- 
				<a href="javascript:void(0);" onclick="javascript:openWindow('playerProfile',{'plid':<?=$v['from_id']?>});"><?=$v['from']?></a>
				-->
				<?=$v['from_url']?>
			</td>
			<td class="date"><?=$v['date']?></td>
			
			<td class="icon"><?=$v['sRemoveUrl']?></td>
		</tr>
		<?php } ?>
	</table>

<?php } ?>

<p>
	<?=$newmsgurl?>
</p>
