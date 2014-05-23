window.TorrentStream = TorrentStream = {};

TorrentStream.Utils = {
    log: function(msg) {
        try {
            console.log(msg);
        }
        catch(e) {
        }
    },
    
    each: function(obj, fn) {
        if(typeof obj !== 'object') {
            return;
        }
        
        if(typeof fn !== 'function') {
            return;
        }

        var name, i = 0, length = obj.length;

        // object
        if (length === undefined) {
            for (name in obj) {
                if (fn.call(obj[name], name, obj[name]) === false) { break; }
            }

            // array
        } else {
            for (var value = obj[0];
                i < length && fn.call( value, i, value ) !== false; value = obj[++i]) {
                }
        }

        return obj;
    },
        
    extend: function(to, from, skipFuncs) {
        if(typeof from !== 'object') {
            return to;
        }

        if(to && from) {
            TorrentStream.Utils.each(from, function(name, value) {
                    if (!skipFuncs || typeof value != 'function') {
                        to[name] = value;
                    }
            });
        }

        return to;
    },
    
    detectPlatform: function() {
        var searchString, platform, navigator = window.navigator;
        
        if(typeof navigator !== 'object') {
            return 'unknown';
        }
        
        if(typeof navigator.platform != 'undefined') {
            searchString = navigator.platform;
        }
        else if(typeof navigator.appVersion != 'undefined') {
            searchString = navigator.appVersion;
        }
        else {
            return 'unknown';
        }
        
        searchString = searchString.toLowerCase();
        if(searchString.indexOf('win') != -1) {
            platform = 'windows';
        }
        else if(searchString.indexOf('mac') != -1) {
            platform = 'mac';
        }
        else if(searchString.indexOf('linux') != -1) {
            platform = 'linux';
        }
        else if(searchString.indexOf('x11') != -1) {
            platform = 'unix';
        }
        else {
            platform = 'unknown';
        }
        
        return platform;
    },
    
    detectBrowser: function() {
        var name = "", version = "";
        
        if(window.ActiveXObject !== undefined) {
            name = "ie";
        }
        else {
            name = "non-ie";
        }
        
        return {
            name: name,
            version: version
        };
    },
    
    detectPluginExt: function() {
        var pluginType = 0,
            pluginEnabled = false,
            isIE = (window.ActiveXObject !== undefined),
            navigator = window.navigator;
            
        if(isIE) {
            try {
                var ax = new ActiveXObject("TorrentStream.TSPlugin.2");
                pluginType = 1;
            }
            catch(e) {}
            
            try {
                var ax = new ActiveXObject("npace_plugin.AceWebPlugin.1");
                pluginType = 2;
            }
            catch(e) {}

            try {
                var ax = new ActiveXObject("npace_plugin.TSMozillaPlugin.1");
                pluginType = 2;
            }
            catch(e) {}
            
            try {
                var ax = new ActiveXObject("npts_plugin.TSMozillaPlugin.1");
                pluginType = 3;
            }
            catch(e) {}
            
            if(pluginType != 0) {
                // consider always enabled in IE
                pluginEnabled = true;
            }
        }
        else {
        
            var mtypes, mime, id, i;
            
            mtypes = [
                {mime: 'application/x-acestream-plugin', id: 2},
                {mime: 'application/x-torrentstream-plugin', id: 3},
                {mime: 'application/x-tstream', id: 1},
            ];
            
            //console.log(navigator.mimeTypes['application/x-acestream-plugin']);
            
            for(i=0; i < mtypes.length; i++) {
                mime = mtypes[i].mime;
                id = mtypes[i].id;
                if(typeof navigator.mimeTypes[mime] !== 'undefined') {
                    pluginType = id;
                    pluginEnabled = !!navigator.mimeTypes[mime].enabledPlugin;
                }
            }
        }
        
        return {
            type: pluginType,
            enabled: pluginEnabled
        };
    },
    
    detectPlugin: function() {
        var ret = this.detectPluginExt();
        retval = (ret.type != 0 && ret.enabled);
        return retval;
    },
    
    // public method for url encoding
	urlEncode: function (string) {
		return escape(this.utf8Encode(string));
	},
 
	// public method for url decoding
	urlDecode: function (string) {
		return this.utf8Decode(unescape(string));
	},
 
	utf8Encode: function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	utf8Decode: function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	},
	
	base64_encode: function(input) {
	    var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	    var output = "";
	    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	    var i = 0;
	    while (i < input.length) {
	        chr1 = input.charCodeAt(i++) & 0xff;
	        chr2 = input.charCodeAt(i++);
	        chr3 = input.charCodeAt(i++);
	        if(!isNaN(chr2)) chr2 &= 0xff;
	        if(!isNaN(chr3)) chr3 &= 0xff;
	        enc1 = chr1 >> 2;
	        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
	        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
	        enc4 = chr3 & 63;
	        if (isNaN(chr2)) enc3 = enc4 = 64;
	        else if (isNaN(chr3)) enc4 = 64;
	        output += _keyStr.charAt(enc1) + _keyStr.charAt(enc2) +	_keyStr.charAt(enc3) + _keyStr.charAt(enc4);
	    }
	    return output;
	},
	
	contentTypeByExtension: function(ext) {
	    if(typeof ext !== 'string' || ext.length == 0) {
	        return 'unknown';
	    }
	    ext = ext.toLowerCase();
		var i,
		    videoExtensions = ['3gp','asf','avi','dv','divx','flv','m2ts','mkv','mpeg','mpeg4','mpegts','mpg4','mp4','mpg','mkv','mov','m4v','ogm','ogv','oga','ogx','qt','rm','swf','ts','vob','wmv','webm'],
		    audioExtensions = ['m4a','mka','aac','ape','flac','flc','mp3','ogg','wav'];
		    
        for(i = 0; i < videoExtensions.length; i++) {
            if(ext == videoExtensions[i]) {
                return 'video';
            }
        }
        
		for(i = 0; i < audioExtensions.length; i++) {
            if(ext == audioExtensions[i]) {
                return 'audio';
            }
        }
        
		return 'other';
	},
	
	ajax: function(conf) {
        var xhr, timer;
        
        var defaultConf = {
            method: "GET",
            timeout: 10,
            binary: false
        };
        conf = TorrentStream.Utils.extend(defaultConf, conf);
        
        if(!conf.url) {
            throw "bad url";
        }
        
        xhr = new window.XMLHttpRequest();
        xhr.open(conf.method, conf.url, true);
        if(conf.binary) {
            // we are downloading binary data
            xhr.overrideMimeType('text/plain; charset=x-user-defined');
        }
        
        if(typeof conf.headers === 'object') {
            for(var name in conf.headers) {
                xhr.setRequestHeader(name, conf.headers[name]);
            }
        }
        
        xhr.onreadystatechange = function() {
            if(xhr.readyState == 4) {
                if(timer) {
                    clearTimeout(timer);
                }
                
                TorrentStream.Utils.log("ajax: request finished: status=" + xhr.status + " url=" + conf.url);
                
                if(xhr.status == 200) {
                    if(typeof conf.success === 'function') {
                        conf.success.call(null, xhr.responseText, xhr);
                    }
                }
                else {
                    if(typeof conf.error === 'function') {
                        conf.error.call(null, xhr);
                    }
                }
            }
        }
        xhr.send();
        
        timer = setTimeout(function() {
                xhr.abort();
                TorrentStream.Utils.log("ajax: request timed out: " + conf.url);
        }, conf.timeout * 1000);
    },
    
    getCookie: function(name, defaultValue)
    {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
        return matches ? decodeURIComponent(matches[1]) : defaultValue;
    },
    
    setCookie: function(name, value, props)
    {
        props = props || {};
        var exp = props.expires;
        if (typeof exp == "number" && exp) {
            var d = new Date();
            d.setTime(d.getTime() + exp*1000);
            exp = props.expires = d;
        }
        if(exp && exp.toUTCString) {
            props.expires = exp.toUTCString();
        }
        
        value = encodeURIComponent(value);
        var updatedCookie = name + "=" + value;
        for(var propName in props) {
            updatedCookie += "; " + propName;
            var propValue = props[propName];
            if(propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }
        document.cookie = updatedCookie;
    },
    
    deleteCookie: function(name)
    {
        TorrentStream.Utils.setCookie(name, null, {expires: -1});
    }
};

TorrentStream.Utils.JSON = {
    parse: function(json) {
        if(typeof JSON === 'object' && typeof JSON.parse === 'function') {
            return JSON.parse(json);
        }
        else {
            if(json.substring(0, 1) !== '(') {
                json = '(' + json + ')';
            }
            return eval(json);
        }
    }
};
