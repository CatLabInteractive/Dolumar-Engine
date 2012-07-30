<p><?=$about?></p>

<?php if (isset ($list_units)) { foreach ($list_units as $v) { ?>
<table class="tlist unitList" style="margin-top: 2px; width: 400px;">
	<tr>
		<th colspan="2" style="border-right: none;"><?=$v[0]?></th>
		<td colspan="2" style="text-align:center;"><?=$v[5]?></td>
		<td style="text-align: right; border-left: none; padding-right: 5px;"><?=$v[4]?></td>
	</tr>

	<tr>
		<td rowspan="2" style="width: 50px; padding: 0px; border-right: none;"><img src="<?=IMAGE_URL?>stats/troop.gif" title="<?=$v[0]?>" style="width: 50px; margin-bottom: 0px;"/></td>
		
		<td class="atdef"><img src="<?=IMAGE_URL?>stats/attack.gif" title="<?=$unit_attack?>"> <?=$v[1]['atAt']?> / <?=$v[1]['atDef']?></td>
		<td class="middle" style="width: 60px; vertical-align: middle; text-align: center;"><img src="<?=IMAGE_URL?>stats/infDef.gif" title="<?=$unit_defIn?>"> <?=$v[1]['defIn']?>%</td>
		<td class="middle" style="width: 60px; text-align: center;"><img src="<?=IMAGE_URL?>stats/ranDef.gif" title="<?=$unit_defAr?>"> <?=$v[1]['defAr']?>%</td>
		<td style="padding-left: 5px; text-align: center;"><img src="<?=IMAGE_URL?>stats/village.gif" title="<?=$unit_inVillage?>"> <?=$v[2]?></img></td>
	</tr>

	<tr>
		<td class="atdef"><img src="<?=IMAGE_URL?>stats/health.gif" title="<?=$unit_health?>"> <?=$v[1]['hp']?></td>
		<td class="bmiddle" style="text-align: center;"><img src="<?=IMAGE_URL?>stats/cavDef.gif" title="<?=$unit_defCav?>"> <?=$v[1]['defCav']?>%</td>
		<td class="bmiddle" style="text-align: center;"><img src="<?=IMAGE_URL?>stats/magDef.gif" title="<?=$unit_defMag?>"> <?=$v[1]['defMag']?>%</td>
		<td style="padding-left: 5px; text-align: center;"><img src="<?=IMAGE_URL?>stats/total.gif" title="<?=$unit_inTotal?>"> <?=$v[3]?></img></td>
	</tr>
</table>
<?php } } else { echo '<p>'.$noUnits.'</p>'; } ?>
