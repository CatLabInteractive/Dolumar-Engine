<?php 
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" ?>'; 
?>
<Module>
  <ModulePrefs 
  	title="Dolumar" 
  	author_email="neuroninteractive@gmail.com"
  	title_url = "http://www.dolumar.be/"
  	
  >
    <Require feature="opensocial-0.7" />
    <Require feature="dynamic-height" />
    <Require feature="views" />
  </ModulePrefs>
  <Content type="html">
    <![CDATA[
    
	<script>
		gadgets.util.registerOnLoadHandler(init);
		
		var currCanvas = gadgets.views.getCurrentView().getName();
		
		function makeCachedRequest(url, callback, params, refreshInterval) {
			var ts = new Date().getTime();
			var sep = "?";
			if (refreshInterval && refreshInterval > 0) {
				ts = Math.floor(ts / (refreshInterval * 1000));
			}
			if (url.indexOf("?") > -1) {
				sep = "&";
			}
			url = [ url, sep, "nocache=", ts ].join("");
			gadgets.io.makeRequest(url, callback, params);
		};
		
		function init ()
		{
			switch (currCanvas)
			{
				case 'canvas':
				
					var params = {};

					params[gadgets.io.RequestParameters.CONTENT_TYPE] = gadgets.io.ContentType.JSON;
					params[gadgets.io.RequestParameters.AUTHORIZATION] = gadgets.io.AuthorizationType.SIGNED;
			
					makeCachedRequest
					(
						'<?=ABSOLUTE_URL?>dispatch.php?module=opensocial/login/',
						onDone,
						params,
						1
					);
				
				break;
				
				case 'profile':
				
					drawPlainMap ();
				
				break;
			}
		}
		
		function onDone (data)
		{
			if (data.data.status == 'success')
			{
				var sessionid = data.data.session_key;
				drawGame (sessionid);
				_IG_AdjustIFrameHeight();
			}
			else
			{
				alert (data.data.msg);
			}
		}
	
		function drawGame (sesId)
		{
			document.getElementById('content').innerHTML = '<iframe style="margin: 0px; padding: 0px; border: none; width: 100%; height: 600px" src="<?=ABSOLUTE_URL?>?phpSessionId='+sesId+'"></iframe>';
			//document.getElementById('content').innerHTML = phpSessionId;
			_IG_AdjustIFrameHeight();
		}
		
		function drawPlainMap ()
		{
			var dims = gadgets.window.getViewportDimensions();
			
			var width = dims.width;
			var height = Math.floor (dims.width / 1.61803399);
			
			//document.getElementById('content').innerHTML = '<iframe style="margin: 0px; padding: 0px; border: none; width: 100%; height: 300px" src="<?=ABSOLUTE_URL?>map/plainmap"></iframe>';
			document.getElementById('content').innerHTML = '<img src="<?=ABSOLUTE_URL?>image/snapshot/?width='+width+'&height='+height+'&zoom=40&logo=true" style="width: '+width+'px; height:'+height+';" />';
			_IG_AdjustIFrameHeight();
		}
		
	</script>
    
    	<div id="content"></div>
    
    ]]>
  </Content>
</Module>
