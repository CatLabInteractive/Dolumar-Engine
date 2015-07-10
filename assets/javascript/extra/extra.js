// Fireworks
Game.extra = {};

Game.extra.fireworks = 
{
	'isPrepared' : false,

	'isHoliday' : function ()
	{
		var today = new Date();
		
		// new year
		if (today.getDate () == 1 && today.getMonth () == 0)
		{
			return true;
		}

		return false;
	},

	'start' : function ()
	{
		if (!Game.extra.fireworks.isPrepared)
		{
			Game.extra.fireworks.isPrepared = true;
	
			Game.core.massInclude
			(
				new Array
				(
					'extra/fireworks/JSFX_Layer.js',
					'extra/fireworks/JSFX_Browser.js',
					'extra/fireworks/JSFX_Fireworks.js'
				),
				Game.extra.fireworks.workfire
			);
		}
		else
		{
			Game.extra.fireworks.workfire ();
		}
	},
	
	'workfire' : function ()
	{
		//JSFX.Fire(40, 100, 100);
		setTimeout("JSFX.Fire(40, 100, 100)", 1000);
	},
	
	'stop' : function ()
	{

	}
}

if (Game.extra.fireworks.isHoliday ())
{
	Game.core.observe ('mapLoad', Game.extra.fireworks.start);
}