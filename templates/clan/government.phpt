<!--<h2><?=$this->getText ('title')?></h2>-->

<ul class="tabs">
	<li>
		<a href="javascript:void(0);" onclick="windowAction(this,{'action':'overview'});"><?=$this->getText ('overview', 'overview'); ?></a>
	</li>
	
	<li class="active">
		<a href="javascript:void(0);" onclick="windowAction(this,{'action':'government'});"><?=$this->getText ('government', 'overview'); ?></a>
	</li>
</ul>

<h3><?=$this->getText ('description')?></h3>
<form onsubmit="return submitForm (this);">
	<fieldset class="simpleform nolegend">	
		<legend><?=$this->getText ('aDescription')?></legend>
		
		<input type="hidden" class="hidden" name="action" value="government" />
		<input type="hidden" class="hidden" name="form" value="description" />
		
		<ol>
			<li>
				<label><?=$this->getText ('lname')?>:</label>
				<input type="text" name="name" value="<?=$name_value?>" />
			</li>
			
			<li>
				<label><?=$this->getText ('ldesc')?>:</label>
				<textarea name="description"><?=$description_value?> </textarea>
			</li>
			
			<li>
				<button type="submit"><?=$this->getText ('descSubmit')?></button>
			</li>
		</ol>
	</fieldset>
</form>

<h3><?=$this->getText ('password')?></h3>
<?php if ($isProtected) { ?>
	<p class="true"><?=$this->getText ('isProtected')?></p>
<?php } else { ?>
	<p class="false"><?=$this->getText ('noPassword')?></p>
<?php } ?>

<form onsubmit="return submitForm(this);">
	<fieldset class="simpleform nolegend">
		<legend><?=$this->getText ('changePassword')?></legend>
		
		<input type="hidden" class="hidden" name="action" value="government" />
		
		<ol>
			<li>
				<label><?=$this->getText ('password')?></label>
				<input type="text" name="password" />
			</li>
			
			<li>
				<button type="submit"><?=$this->getText ('submit')?></button>
			</li>
		</ol>
	</fieldset>
</form>

<?php if (isset ($list_members)) { ?>
	<h3><?=$this->getText ('usermanager')?></h3>
	
	<p class="false"><?=$this->putIntoText
	(
		$this->getText ('kick_warning'),
		array
		(
			'own_clanhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_OWN_CLANHOP,
			'clanhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_CLANHOP,
			'joinhop' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_JOINS,
			'kicked' => Dolumar_Players_Clan::MIN_DAYS_BETWEEN_KICKJOIN
		)
	)?>
	</p>
	
	<form onsubmit="return submitForm(this);" class="membermanager">
		<fieldset>
			<input type="hidden" class="hidden" name="action" value="government" />
			<input type="hidden" class="hidden" name="form" value="usermanagement" />

			<ol>
			
				<?php foreach ($list_members as $v) { ?>
					<li>
						<label><a href="javascript:void(0);" onclick="openWindow('playerProfile',{'plid':<?=$v['id']?>});"><?=$v['name']?></a></label>
						<select name="member_<?=$v['id']?>">
							<?php foreach ($roles as $role) { ?>
								<option <?php if ($role == $v['role']) { echo 'selected="selected"'; } ?> value="<?=$role?>"><?=$this->getText ($role, 'roles')?></option>
							<?php } ?>
						</select>
					</li>
				<?php } ?>
				
				<li>
					<button type="submit"><?=$this->getText ('storeChanges')?></button>
				</li>
			</ol>
		</fieldset>
	</form>
<?php } ?>

<!--
<?php $overview = $this->getClickTo ('toOverview'); ?>
<p><?=$overview[0]?><a href="javascript:void(0);" onclick="windowAction(this,{'action':'overview'});"><?=$overview[1]?></a><?=$overview[2]?></p>
-->
