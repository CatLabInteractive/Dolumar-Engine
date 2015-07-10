Game.gui =
{

	'objLoadingDialog' : null,
	
	'aOpenWindows' : new Array (),
	
	'bIsInDialog' : false,
	'iLoadingIcons' : 0,
	
	'sGuiTheme' : 'dolumar',
	
	'objCountDown' : null,
	'objFastCount' : null,
	
	'aPools' : new Array (),
	'iPollInterval' : new Array (),
	'iPollCountDown' : new Array (),
	
	'aRunningPolls' : new Array (),
	
	'gaTracker' : null,
	'fTracker' : function (sData) {},
	
	'lastActivity' : new Date (),
	'maxIdleSeconds' : 60 * 30,

	'initialize' : function ()
	{		
		// Periodical poller
		Game.gui.objCountDown = new PeriodicalExecuter (Game.gui.doPollActions, 1);
		Game.gui.objFastCount = new PeriodicalExecuter (Game.gui.doFastPollActions, 0.1);
	},
	
	// Periodical triggered function
	'doPollActions' : function ()
	{
		Game.gui.updateCounters ();
		Game.gui.updateClocks ();
		Game.gui.blinkTheBlinkers ();
		
		// Check for polling
		for (var pool = 0; pool < Game.gui.aPools.length; pool ++)
		{
			if (Game.gui.iPollInterval[Game.gui.aPools[pool]] > 0)
			{
				if (Game.gui.iPollCountDown[Game.gui.aPools[pool]] == 0)
				{
					Game.gui.iPollCountDown[Game.gui.aPools[pool]] = Game.gui.iPollInterval[Game.gui.aPools[pool]];
				
					// Only do an automatic call if there are no other calls running
					if (typeof (Game.gui.aRunningPolls[Game.gui.aPools[pool]]) == 'undefined' ||
						Game.gui.aRunningPolls[Game.gui.aPools[pool]] <= 0)
					{
						Game.gui.doDialogRequest (null, Game.gui.aPools[pool]);
					}
				}
				else
				{
					Game.gui.iPollCountDown[Game.gui.aPools[pool]] --;
				}
			}
		}
	},
	
	'doFastPollActions' : function ()
	{
		Game.gui.updateIncreasing ();
	},
	
	'blinkTheBlinkers' : function ()
	{
		var elements = $$('.blink');
		for (var i = 0; i < elements.length; i ++)
		{
			// Set opaticity to 0.
			elements[i].setOpacity (1);
			Effect.Pulsate(elements[i], { pulses: 1, duration: 0.5 });
		}
	},
	
	// Search for all countdowns and update them
	'updateCounters' : function ()
	{
		var cs = $$('.counter');
		var hasReachedZero = false;
		
		var now = Math.floor ((new Date()).getTime () / 1000);
		
		for (var i = 0; i < cs.length; i ++)
		{
			if (typeof(cs[i].counter) == 'undefined')
			{
				var sp = cs[i].innerHTML.split (':');
				cs[i].counter = parseInt(sp[0], 10) * 3600 + parseInt(sp[1], 10) * 60 + parseInt(sp[2], 10);
				cs[i].iStartDate = Math.floor ((new Date()).getTime() / 1000);
			}
			
			else if (cs[i].counter != false)
			{
				var counter = cs[i].counter - (now - cs[i].iStartDate);
		
				if (counter >= 0)
				{						
					var h = Math.floor (counter / 3600);
					var m = Math.floor ((counter - h * 3600) / 60);
					var s = counter - h * 3600 - m * 60;
			
					if (h < 10) h = '0' + h;
					if (m < 10) m = '0' + m;
					if (s < 10) s = '0' + s;
			
					cs[i].innerHTML = h + ':' + m + ':' + s;
				}
				else
				{
					// Put the counter to false.
					cs[i].counter = false;
					hasReachedZero = true;
				}
			}
		}
		
		if (hasReachedZero)
		{
			Game.gui.doDialogRequest ();
		}
	},
	
	/*
		Search & update all increasings
	*/
	'updateIncreasing' : function ()
	{
		var increasings = $$('.increasing');
		var now = new Date ();
		
		for (var i = 0; i < increasings.length; i ++)
		{
			var increasing = increasings[i];
			
			if 
			(
				typeof (increasing.initial) == 'undefined' || 
				typeof (increasing.startdate) == 'undefined' || 
				typeof (increasing.income) == 'undefined'
			)
			{
				var data = increasing.title.split ('/');
			
				increasing.initial = parseFloat(increasing.innerHTML);
				increasing.startdate = now.getTime ();
				increasing.income = parseFloat(data[0]);
				
				if (typeof (data[1]) != 'undefined')
				{
					increasing.maximum = parseInt (data[1]);
				}
				else
				{
					increasing.maximum = 0;
				}
				
				increasing.title = null;
			}
			
			var timepassed = (now.getTime() - increasing.startdate) / 1000;
			
			//alert (timepassed);
			var newval = Math.floor (increasing.initial + ((timepassed / (60*60)) * increasing.income));
			if (newval <= increasing.maximum || increasing.maximum == 0)
			{
				increasing.innerHTML = newval;
			}
		}
	},
	
	/*
		Search & update all clocks
	*/
	'updateClocks' : function ()
	{
		var clocks = $$('.clock');
		
		for (var i = 0; i < clocks.length; i ++)
		{
			var clock = clocks[i];
			
			var now = new Date ();
			var format = clock.attributes.format.value;
		
			// Check for date
			if (typeof (clock.difference) == 'undefined')
			{
				// Fetch the whole day.
				//CONFIG_DATETIME_FORMAT
				
				// Loop trough the format & fetch all important data
				var data =
				{
					'Y' : now.getFullYear (),
					'm' : now.getMonth (),
					'd' : now.getDate (),
					'H' : now.getHours (),
					'i' : now.getMinutes (),
					's' : now.getSeconds ()
				}
				
				var sc = 0;
				var str = clock.innerHTML;
				
				for (var j = 0; j < format.length; j ++)
				{
					var fchar = format.charAt(j);
					switch (fchar)
					{
						case 'Y':
							data[fchar] = str.substr (sc, 4);
							sc += 4;
						break;
						
						case 'm':
						case 'd':
						case 'H':
						case 'i':
						case 's':
							data[fchar] = str.substr (sc, 2);
							sc += 2;
						break;
						
						default:
							sc ++;
						break;
					}
				}
				
				var remDate = new Date ();
				remDate.setFullYear (data['Y']);
				remDate.setMonth ((data['m'] - 1));
				remDate.setDate (data['d']);
				remDate.setHours (data['H']);
				remDate.setMinutes (data['i']);
				remDate.setSeconds (data['s']);
				
				clock.difference = now.getTime() - remDate.getTime();
			}
			
			// Replace inner html
			now.setTime(now.getTime() - clock.difference);
			
			// Replace the HTML
			var output = "";
			
			for (var j = 0; j < format.length; j ++)
			{
				var fchar = format.charAt(j);
				switch (fchar)
				{
					case 'Y':
						output = output + now.getFullYear();
					break;
					
					case 'm':
						output = output + Game.core.leadingZero(now.getMonth() + 1);
					break;
					
					case 'd':
						output = output + Game.core.leadingZero(now.getDate());
					break;
					
					case 'H':
						output = output + Game.core.leadingZero(now.getHours());
					break;
					
					case 'i':
						output = output + Game.core.leadingZero(now.getMinutes());
					break;
					
					case 's':
						output = output + Game.core.leadingZero(now.getSeconds());
					break;
					
					default:
						output = output + fchar;
					break;
				}
			}
			
			clock.innerHTML = output;
		}
	}
}
Event.observe (window, 'load', Game.gui.initialize, false);

Game.admin = 
{
	'askReason' : function (form)
	{
		var reason = prompt ("Please provide a reason for this action.");
		
		var input = document.createElement ('input');
		input.name = 'reason';
		input.value = reason;
		input.type = 'hidden';
		form.appendChild (input);
	}
}
