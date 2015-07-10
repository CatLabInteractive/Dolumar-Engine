<p>
	<?=$welcome?>
</p>

<?php if (isset ($setEmail)) { ?>
	<h2><?=$todo?></h2>
	<ul>
		<li>&raquo; <a href="javascript:void(0);" onclick="windowAction(this,'formAction=email');"><?=$setEmail?></a></li>
	</ul>
<?php } ?>

<h2><?=$this->getText ('account')?></h2>
<ul>
	<li>
		&raquo; <a href="javascript:void(0);" onclick="openWindow('Premium');"><strong><?=$this->getText ('premium')?></strong></a>
	</li>
	
	<?php if (isset ($changePassword)) { ?>
		<li>
			&raquo; <a href="javascript:void(0);" onclick="windowAction (this, {'action':'changePassword'});"><?=$this->getText ('changePass')?></a>
		</li>
	<?php } ?>
</ul>

<ul style="margin-top: 8px;">
	<li>
		&raquo; <a href="javascript:void(0);" onclick="openWindow('Vacation');"><?=$this->getText ('vacationMode')?></a>
	</li>
	<li>
		&raquo; <a href="javascript:void(0);" onclick="windowAction (this, {'action':'resetAccount'});"><?=$this->getText ('resetAccount')?></a>
	</li>
</ul>

<ul style="margin-top: 8px;">
	<li>
		&raquo; <a href="<?=ABSOLUTE_URL?>?logout=true"><?=$logout?></a>
	</li>
</ul>
