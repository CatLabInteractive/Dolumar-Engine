<?php $this->setTextSection ('overview', 'clan'); ?>

<!--<h2><?=Neuron_Core_Tools::putIntoText ($this->getText ('clan'), array ('clan' => $clanname));?></h2>-->

<ul class="tabs">
	<li class="active">
		<a href="#"><?=$this->getText ('overview'); ?></a>
	</li>
	
	<?php if ($canGovern) { ?>
	<li>
		<a href="javascript:void(0);" onclick="windowAction(this,{'action':'government'});"><?=$this->getText ('government'); ?></a>
	</li>
	<?php } ?>
</ul>

<?php if (isset ($error)) { ?>
	<p class="false"><?=$error?></p>
<?php } ?>

<?php if (isset ($description)) { ?>
	<?=$description?>
<?php } ?>

<?php $toForum = $this->getClickTo ('toForum'); ?>
<?php $toLogs = $this->getClickTo ('toLogs'); ?>

<!--
<p>
	<?=$toForum[0]?><a href="javascript:void(0);" onclick="openWindow('clanForum', {'clan':<?=$clanid?>});"><?=$toForum[1]?></a><?=$toForum[2]?>
	
	<br /><?=$toLogs[0]?><a href="javascript:void(0);" onclick="openWindow('Clanlogs');"><?=$toLogs[1]?></a><?=$toLogs[2]?>
	
	<?php if ($canGovern) { $toGovern = $this->getClickTo ('toGovern');?>
		<br /><?=$toGovern[0]?><a href="javascript:void(0);" onclick="windowAction(this,{'action':'government'});"><?=$toGovern[1]?></a><?=$toGovern[2]?>
	<?php } ?>
</p>
-->

<?php if (isset ($list_members)) { ?>
	<h3><?=$this->getText ('members')?></h3>
	<ul class="clanmembers">
		<?php foreach ($list_members as $v) { ?>
			<li>
				<span class="clanmember <?=$v['status']?>" title="<?=$v['status_t']?>">
					<a href="javascript:void(0);" onclick="openWindow('playerProfile',{'plid':<?=$v['id']?>});"><?=$v['name']?></a>
					<span> (<?=$v['status_t']?>)</span>
				</span>
				
				<span class="online-status <?=$v['online']?>" title="<?=$this->getText ($v['online'], 'onlinestatus', 'main')?>">
					<span> (<?=$this->getText ($v['online'], 'onlinestatus', 'main')?>)</span>
				</span>
			</li>
		<?php } ?>
	</ul>
<?php } ?>

<div class="clearer"></div>
<h3><?=$this->getText ('actions'); ?></h3>

<ul class="actions">
	<li>
		<a href="javascript:void(0);" onclick="openWindow('clanForum', {'clan':<?=$clanid?>});"><?=$this->getText ('forum'); ?></a>
	</li>
	
	<li>
		<a href="javascript:void(0);" onclick="openWindow('Clanlogs');"><?=$this->getText ('logs'); ?></a>
	</li>

	<?php if ($canJoin) { $toJoin = $this->getClickTo ('toJoin'); ?>
		<li>
			<a href="javascript:void(0);" onclick="windowAction(this,{'action':'join'});"><?=$this->getText ('join')?></a>
		</li>
	<?php } elseif ($canLeave) { $toLeave = $this->getClickTo ('toLeave'); ?>
		<li><a href="javascript:void(0);" onclick="confirmAction(this,{'action':'leave'}, '<?=addslashes($this->putIntoText 
		(
			$this->getText ('conLeave'), 
			array 
			(
				'own_clanhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_OWN_CLANHOP,
				'clanhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_CLANHOP
			)
		))?>');"><?=$this->getText ('leave');?></a></li>
	<?php } ?>

</ul>
