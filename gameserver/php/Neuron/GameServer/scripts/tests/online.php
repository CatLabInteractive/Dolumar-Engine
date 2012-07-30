<!DOCTYPE html>
<html>
  <head>
    <title>Online Players</title>
    <meta http-equiv="refresh" content="30">
  </head>
  <body style="background: #eee;">
  	<p style="position: absolute; top: 50%; margin-top: -100px; left: 50%; margin-left: -200px; color: gray; width: 400px; text-align: center; font-size: 150px; font-family: Verdana;">
	<?php
		echo Neuron_GameServer::getServer ()->countOnlineUsers ();
	?>
	</p>
  </body>
</html>

