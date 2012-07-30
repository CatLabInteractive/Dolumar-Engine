//var CONFIG_IS_PREMIUM = true;

if (false && !CONFIG_IS_PREMIUM /* && !$(document.body).hasClassName ('no-advertisement') */)
{
	Game.gui.observe 
	(
		'showLoadingDialog', 
		function (args)
		{
			Game.gui.fTracker ('advertisement');
		
			var dialog = args.shift();
			dialog.setSize (300, 300);
		
			var iframe = document.createElement ('iframe');
		
			iframe.src = CONFIG_GAME_URL + 'page/advertisement/?session_id='+RUNTIME_SESSION_ID;
			iframe.id = 'advertisement_iframe';
		
			iframe.style.width = '300px';
			iframe.style.height = '250px';
			iframe.style.border = '0px none black';
			iframe.style.marginBottom = '5px';
			iframe.style.overflow = 'hidden';
			iframe.scrolling = 'no';
			iframe.frameBorder = 0;
		
			var children = $(dialog.content).childElements ();
		
			dialog.content.appendChild (iframe);
		
			var elements = [];
		
			for (var i = 0; i < children.length; i ++)
			{
				dialog.content.removeChild (children[i]);
				dialog.content.appendChild (children[i]);
			}
		}
	);

	Game.gui.observe 
	(
		'hideLoadingDialog', 
		function (args)
		{
			var ads = $('advertisement_iframe');
			ads.parentNode.removeChild (ads);
		}
	);

	// Delay the map load algorithm
	Game.map.fStartTimeout = 5;
	Game.map.fTimeoutSeconds = 2;

	function loadads ()
	{
		Game.gui.showLoadingDialog ();
	
		setTimeout 
		(
			function ()
			{
				Game.gui.closeLoadingDialog ();
				Game.map.fTimeoutSeconds = 0.1;
			},
			20000
		);
	}

	Event.observe (window, 'load', loadads);
}
