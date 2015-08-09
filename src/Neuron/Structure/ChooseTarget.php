<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class Neuron_Structure_ChooseTarget
{
	private $aInput;
	private $bTargetSelf;
	private $objVillage;
	private $bShowTargets;
	
	private $sReturnAction;
	private $sReturnText;

	public function __construct ($aInput, $objVillage, $canTargetSelf = false, $bShowTargets = true)
	{
		$this->aInput = $aInput;
		$this->objVillage = $objVillage;
		$this->bTargetSelf = $canTargetSelf;
		$this->bShowTargets = $bShowTargets;
	}
	
	public function setReturnData ($returnData, $returnText = null)
	{
		$this->sReturnAction = str_replace ('"', "'", json_encode ($returnData));
		
		if (isset ($returnText))
		{
			$this->sReturnText = $returnText;
		}
		else
		{
			$text = Neuron_Core_Text::__getInstance ();
			$this->sReturnText = $text->get ('return', 'chooseTarget', 'main');
		}
	}
	
	public function getHTML ($error = null)
	{
		$page = new Neuron_Core_Template ();
		$page->setTextSection ('chooseTarget', 'main');
		
		$page->set ('canTargetSelf', $this->bTargetSelf);
		$page->set ('vid', $this->objVillage->getId ());
		
		// Return action
		if (isset ($this->sReturnAction))
		{
			$page->set ('returnUrl', $this->sReturnAction);
			$page->set ('returnText', $this->sReturnText);
		}
		
		$sQuery = null;
		
		if (isset ($this->aInput['sVillageName']))
		{
			$sQuery = $this->aInput['sVillageName'];
			unset ($this->aInput['sVillageName']);
		}
				
		$page->set ('input', $this->aInput);
		$page->set ('query', Neuron_Core_Tools::output_varchar ($sQuery));
		
		// Fetch all troops
		if (!empty ($sQuery))
		{
			$db = Neuron_Core_Database::__getInstance ();
			
			$page->set ('hasSearched', true);
			
			$l = $db->getDataFromQuery
			(
				$db->customQuery
				("
					SELECT
						*
					FROM
						villages
					WHERE
						vname LIKE '%".$db->escape ($sQuery)."%'
						AND isActive = 1
					ORDER BY
						FIELD(vname, '".$db->escape ($sQuery)."', vname),
						vname ASC
					LIMIT 10
				")
			);
			
			if (count ($l) > 0)
			{
				foreach ($l as $v)
				{
					$village = Dolumar_Players_Village::getVillage ($v['vid'], false);
					$village->setData ($v);
					
					$tc = $village->buildings->getTownCenter ();
					
					if ($tc)
					{
						$loc = $tc->getLocation ();
					
						$page->addListValue
						(
							'results',
							array
							(
								'id' => $village->getId (),
								'name' => Neuron_Core_Tools::output_varchar ($village->getName ()),
								'location' => $loc[0] . ','.$loc[1]
							)
						);
					}
				}
			}
		}
		
		elseif ($this->bShowTargets)
		{
			$db = Neuron_DB_Database::__getInstance ();
		
			// Popular targets
			/*
			$l = $db->getDataFromQuery
			(
				$db->customQuery
				("
					SELECT
						l_vid,
						villages.*
					FROM
						game_log
					LEFT JOIN
						villages ON game_log.l_vid = villages.vid
					WHERE
						(l_action = 'attack' OR l_action = 'defend') 
						AND l_vid != ".$this->objVillage->getId ()."
						AND l_subId = ".$this->objVillage->getId ()."
						AND isActive = 1
					GROUP BY
						l_vid
					ORDER BY
						l_date ASC
					LIMIT 10
				")
			);
			*/
			
			$l = $this->objVillage->visits->getLastVisits ();
			
			if (count ($l) > 0)
			{
				foreach ($l as $village)
				{					
					// Only add active villages
					if ($village->isActive ())
					{
						$tc = $village->buildings->getTownCenter ();
						if ($tc)
						{
							$loc = $tc->getLocation ();
					
							$page->addListValue
							(
								'results',
								array
								(
									'id' => $village->getId (),
									'name' => Neuron_Core_Tools::output_varchar ($village->getName ()),
									'location' => $loc[0] . ','.$loc[1]
								)
							);
						}
					}
				}
			}
		}
		
		if (isset ($error))
		{
			$page->set ('external_error', $error);
		}
		
		return $page->parse ('neuron/structure/chooseTarget.phpt');
	}
}
?>
