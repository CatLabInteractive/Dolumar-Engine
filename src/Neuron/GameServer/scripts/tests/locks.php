<?php

session_write_close ();

$action = isset ($_GET['action']) ? $_GET['action'] : null;
$lockid = isset ($_GET['lock']) ? $_GET['lock'] : time ();

if ($action == 'lock')
{
	$lock = Neuron_Core_Lock::getInstance ('test');
	if ($lock->setLock ('test', $lockid))
	{
		usleep (2 * 1000000);
		echo 'Here I am!';
		usleep (2 * 1000000);
		
		$lock->releaseLock ('test', $lockid);
	}
	else
	{
		echo 'Was locked :(';
	}
	echo '<br />Lock ID: '.$lockid;
}

else 
{
	for ($i = 0; $i < 20; $i ++)
	{
		echo '<iframe src="'.ABSOLUTE_URL.'test/locks/?action=lock&lock='.$lockid.'"></iframe>';
	}
}

?>
