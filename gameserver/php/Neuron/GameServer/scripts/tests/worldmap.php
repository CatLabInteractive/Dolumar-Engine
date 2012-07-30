<html>
	<head>
		<title>World Map</title>
		
		<style>
			*
			{
				margin: 0px;
				padding: 0px;
			}
			
			td, tr, table
			{
				border: 0px solid none;
				border-collapse: collapse;
			}
		</style>
	</head>
	<body>	
		<?php $amount = 8; ?>
	
		<table>
			<?php for ($i = ceil($amount / 2); $i > 0 - ceil($amount / 2); $i --) { ?>
				<tr>
					<?php for ($j = 0 - ceil($amount / 2); $j < ceil($amount / 2); $j ++) { ?>
						<td><img src="<?=ABSOLUTE_URL?>image/world/?x=<?=$j?>&y=<?=$i?>"></td>
					<?php } ?>
				</tr>
			<?php } ?>	
		</table>
	</body>
</html>
