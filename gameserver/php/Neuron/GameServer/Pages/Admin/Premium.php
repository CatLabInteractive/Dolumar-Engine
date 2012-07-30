<?php
class Neuron_GameServer_Pages_Admin_Premium extends Neuron_GameServer_Pages_Admin_Page
{
	public function getBody ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				players
			WHERE
				premiumEndDate > FROM_UNIXTIME('".NOW."')
		");
		
		$refund = Neuron_Core_Tools::getInput ('_GET', 'refund', 'int');
		$refund = $refund == 1;
		
		$page = new Neuron_Core_Template ();
		
		foreach ($data as $v)
		{
			$player = Neuron_GameServer::getPlayer ($v['plid'], $v);
			
			$date = $player->getPremiumEndDate ();
			$diff = $date - NOW;
			
			$amounts = ceil ($diff / (60*60*24*15));
			$credits = $amounts * PREMIUM_COST_CREDITS;
			
			if ($refund)
			{
				$amref = $player->refundCredits ($credits, 'premium account refund');
			}
			else
			{
				$amref = false;
			}
			
			$page->addListValue
			(
				'players',
				array
				(
					'name' => $player->getDisplayName (),
					'enddate' => date ('d m Y H:i:s', $date),
					'credits' => $credits,
					'refunded' => $amref
				)
			);
		}
		
		return $page->parse ('pages/admin/premium/premium.phpt');
	}
}
?>
