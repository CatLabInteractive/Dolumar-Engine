<?php $this->setTextSection ('form', 'search'); ?>

<div class="searchbox">
	<form method="post" class="searchbox" onsubmit="return submitForm(this);">
		<fieldset>
			<legend><?=$this->getText ('searchbox'); ?></legend>
	
			<ol>
				<li class="name">
					<label for="search_name"><?=$this->getText ('name'); ?></label>
					<input type="text" id="search_name" name="search_name" />
				</li>
				
				<li class="village">
					<label for="search_village"><?=$this->getText ('village'); ?></label>
					<input type="text" id="search_village" name="search_village" />
				</li>
			
				<li class="race">
					<label for="search_race"><?=$this->getText ('race'); ?></label>
				
					<select id="search_race" name="search_race">
						<option value="0">&nbsp;</option>
						<option value="darkelves">Dark elves</option>
					</select>
				</li>
				
				<li class="online">
					<label for="search_online"><?=$this->getText ('online'); ?></label>
				
					<select id="search_online" name="search_online">
						<option value="0">&nbsp;</option>
						<option value="darkelves">Online now</option>
					</select>
				</li>
			</ol>
		</fieldset>
	
		<fieldset>
			<legend><?=$this->getText ('distance')?></legend>
			<ol>
			
				<li class="distance minimum-distance">
					<label for="search_distance_min"><?=$this->getText ('mindistance'); ?></label>
					<input type="text" name="search_distance_min" id="search_distance_min" />
				</li>
			
				<li class="distance maximum-distance">
					<label for="search_distance_max"><?=$this->getText ('maxdistance'); ?></label>
					<input type="text" name="search_distance_max" id="search_distance_max" />
				</li>
			
				<li class="ankerpoint">
					<label for="search_ankerpoint"><?=$this->getText ('ankerpoint'); ?></label>
				
					<select for="search_ankerpoint">
						<option value="darkelves">Daedeloth's village</option>
					</select>
				</li>
			</ol>
		</fieldset>
		
		<fieldset>
			<legend><?=$this->getText ('networth')?></legend>
			<ol>
			
				<li class="networth minimum-networth">
					<label for="search_networth_min"><?=$this->getText ('minnetworth'); ?></label>
					<input type="text" name="search_networth_min" id="search_networth_min" />
				</li>
			
				<li class="networth maximum-networth">
					<label for="search_distance"><?=$this->getText ('maxnetworth'); ?></label>
					<input type="text" name="search_networth_max" id="search_networth_max" />
				</li>
			</ol>
		</fieldset>
		
		<fieldset class="buttons">
			<ol>
				<li>
					<button type="submit"><?=$this->getText ('search'); ?></button>
				</li>
			</ol>
		</fieldset>
	</form>
</div>
