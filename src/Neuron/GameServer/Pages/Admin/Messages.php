<?php
class Neuron_GameServer_Pages_Admin_Messages extends Neuron_GameServer_Pages_Admin_Page
{
	public function getBody ()
	{
		$function = create_function
		(
			'$data,$title',
			'
				$query = "";
				foreach ($data as $k => $v)
				{
					$query .= $k . "=" . urlencode ($v) . "&";
				}
				$query = substr ($query, 0, -1);
			
				if (is_array ($title))
				{
					return $title[0].\'<a href="\'.ABSOLUTE_URL.\'admin/messages/?\'.$query.\'">\'.$title[1].\'</a>\'.$title[2];
				}
				else
				{
					return \'<a href="\'.ABSOLUTE_URL.\'admin/messages/?\'.$query.\'">\'.$title.\'</a>\';
				}
			'
		);
		
		$function2 = create_function
		(
			'$userid,$title',
			'			
				return \'<a href="\'.ABSOLUTE_URL.\'admin/user/?id=\'.$userid.\'">\'.$title.\'</a>\';
			'
		);
	
		$objMessages = new Neuron_Structure_Messages (Neuron_GameServer::getPlayer (), 25);
		
		$objMessages->setGetUrl ($function);
		$objMessages->setUserUrl ($function2);
		
		return $objMessages->getPageHTML ($_GET);
	}
}
?>
