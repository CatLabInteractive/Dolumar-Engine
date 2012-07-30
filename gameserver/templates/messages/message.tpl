<div class="fancybox">
	<table>

		<tr>
			<td style="width: 25%;"><?=$from?>:</td>
			<td><?=$from_name?></td>
		</tr>
	
		<tr>
			<td><?=$to?>:</td>
			<td><?=$to_name?></td>
		</tr>
		
	</table>
	
	<h3><?=$subject?></h3>
	<div class="message">
		<?=$message?>
	</div>

	<p style="text-align: right;">
		<?=$replyUrl?> | 
		<?=$removeUrl?>
	</p>
</div>

<?php if (isset ($toInbox)) { ?>
	<p><?=$toInboxUrl?></p>
<?php } elseif (isset ($toOutbox)) { ?>
	<p><?=$toOutboxUrl?></p>
<?php } ?>
