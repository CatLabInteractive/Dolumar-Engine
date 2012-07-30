/** 
*  Script lazy loader 0.5
*  Copyright (c) 2008 Bob Matsuoka
*
*  This program is free software; you can redistribute it and/or
*  modify it under the terms of the GNU General Public License
*  as published by the Free Software Foundation; either version 2
*  of the License, or (at your option) any later version.
*/
 
var LazyLoader = {}; //namespace
LazyLoader.timer = {};  // contains timers for scripts
LazyLoader.scripts = [];  // contains called script references
LazyLoader.load = function(url, callback) {
        // handle object or path
        var classname = null;
        var properties = null;
        try {
                // make sure we only load once
                if ($A(LazyLoader.scripts).indexOf(url) == -1) {
                        // note that we loaded already
                        LazyLoader.scripts.push(url);
                        var script = document.createElement("script");
                        script.src = url;
                        script.type = "text/javascript";
                        $$("head")[0].appendChild(script);  // add script tag to head element
                       
                        // was a callback requested
                        if (callback) {    
                                // test for onreadystatechange to trigger callback
                                script.onreadystatechange = function () {
                                        if (script.readyState == 'loaded' || script.readyState == 'complete') {
                                                callback();
                                        }
                                }                            
                                // test for onload to trigger callback
                                script.onload = function () {
                                        callback();
                                        return;
                                }
                                // safari doesn't support either onload or readystate, create a timer
                                // only way to do this in safari
                                if ((Prototype.Browser.WebKit && !navigator.userAgent.match(/Version\/3/)) || Prototype.Browser.Opera) { // sniff
                                        LazyLoader.timer[url] = setInterval(function() {
                                                if (/loaded|complete/.test(document.readyState)) {
                                                        clearInterval(LazyLoader.timer[url]);
                                                        callback(); // call the callback handler
                                                }
                                        }, 10);
                                }
                        }
                } else {
                        if (callback) { callback(); }
                }
        } catch (e) {
                alert(e);
        }
}
