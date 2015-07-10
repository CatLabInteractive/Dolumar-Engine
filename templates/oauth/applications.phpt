<?php foreach ($applications as $app_data) { ?>
	<h2>Application <?php echo $app_data['ID']; ?></h2>
	<table>
		<?php foreach ($app_data as $k => $v) { ?>
			<tr>
				<th>
					<?php echo $k; ?>
				</th>

				<td>
					<?php echo $v; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>