<?php if (isset ($list_msgs)) { ?>
	<?php foreach ($list_msgs as $v) { ?>
	<div class="chat-message <?=$v['class']?> <?= $v['isMine'] ? 'mine' : null ?>">	
		<?php if ($v['class'] != 'all me') { ?>
			<p class="date"><?=$v['date']?></p>
			<p class="username">
				<?php if ($v['plid'] == 0) { ?>
					<?=$v['nickname']?>
				<?php } else { ?>
					<?php if ($v['class'] == 'message') { ?>
						<?php if ($v['isMine']) { ?>
							→ <?=$v['target']?>
						<?php } else { ?>
							← <?=$v['nickname']?>
						<?php } ?>
					<?php } elseif ($v['class'] == 'clan') { ?>
						[<?=$v['nickname']?>]
					<?php } else { ?>
						<?=$v['nickname']?>
					<?php } ?>
				<?php } ?>
			</p>
		<?php } ?>
	
		<?=$v['message']?>
	</div>
	<?php } ?>
<?php } ?>
