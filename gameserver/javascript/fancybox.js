if (!Prototype.Browser.IE6)
{
	Game.gui.observe
	(
		'update',
		function (eli)
		{
			var boxes = $$('.fancybox');
		
			for (var i = 0; i < boxes.length; i ++)
			{
				if (!boxes[i].hasClassName ('is-processed'))
				{
					var container = boxes[i];
					
					container.addClassName ('is-processed');
			
					// Fetch content before going on
					var content = document.createElement ('div');
					content.innerHTML = boxes[i].innerHTML;
					content.className = 'fancybox-content';
				
					container.innerHTML = '';
			
					var top = document.createElement ('div');
					top.className = 'fancybox-top';
					top.innerHTML = '<div class="fancybox-leftcorner"></div><div class="fancybox-rightcorner"></div>';
					container.appendChild (top);
				
					var cr = document.createElement ('div');
					cr.className = 'fancybox-leftcorner';
					var cl = document.createElement ('div');
					cl.className = 'fancybox-rightcorner';
				
					var middle = document.createElement ('div');
					middle.className = 'fancybox-middle';
					
					container.appendChild (middle);
					middle.appendChild (cl);
					cl.appendChild (cr);
					cr.appendChild (content);
				
					var bottom = document.createElement ('div');
					bottom.className = 'fancybox-bottom';
					bottom.innerHTML = '<div class="fancybox-leftcorner"></div><div class="fancybox-rightcorner"></div>';
					container.appendChild (bottom);
					
					container.addClassName ('fancybox-checked');
				}
			}
		}
	);
}
