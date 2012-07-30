Game.core.observe 
(
	'onKonamiCode', 
	function () 
	{
		var div = $(document.createElement ('div'));
		div.id = 'konami_teaser';
		
		var img = $(document.createElement ('img'));
		img.style.margin = '0px';
		img.style.display = 'block';
		
		var overlay = $('overlay');
		
		var padding = 15;
		
		img.observe
		(
			'load',
			function ()
			{
				var size =  Game.core.getWindowSize ();
			
				div.style.position = 'absolute';
				div.style.zIndex = 500001;
				
				var mx = (0 - (parseInt (img.width) / 2));
				var my = (0 - (parseInt (img.height) / 2));
				
				var px = size[0] / 2;
				var py = size[1] / 2;
				
				div.style.border = '1px solid gray';
				div.style.padding = padding + 'px';
				
				div.style.left = Math.floor(px + mx) + 'px';
				div.style.top = Math.floor(py + my) + 'px';
				
				if (overlay)
				{
					overlay.style.width = size[0] + 'px';
					overlay.style.height = size[1] + 'px';
					overlay.style.display = 'block';
				}
				
				document.observe 
				(
					'click', 
					function () 
					{
						div.parentNode.removeChild (div); 
						
						if (overlay)
						{
							overlay.style.display = 'none';
						}
					}
				);
				
				document.body.appendChild (div);
			}
		);
		
		img.src = 'http://master.dolumar.be/images/catlabs.jpg';
		
		div.appendChild (img);
	}
);
