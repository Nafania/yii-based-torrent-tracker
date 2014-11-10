TorrentStream.Player = function(container, conf)
{
    function _log(msg) {
        if(!conf.debug) {
            return;
        }
        
        try {
            if(!msg) {
                msg = "";
            }
            msg = "Player::" + msg;
            console.log(msg);
        }
        catch(e) {}
    }
    
    var undefined,
    self = this,
    content,
    eventHandlers = [],
    _browser,
    _platform,
    _lastMediaData = null,
    _mediaData = null,
    _playerBlocked = false,
    _forceAutoplay = false,
    
    _lastStartedItem = -1,
    _bgprocessStatusCounter = 0,
    
    VER_1_0_2 = getVersion("1.0.2"),
    VER_1_0_3 = getVersion("1.0.3"),
    VER_1_0_4 = getVersion("1.0.4"),
    VER_1_0_5 = getVersion("1.0.5"),
    VER_2_0_10 = getVersion("2.0.10"),
        
    MEDIA_TYPE = {
            TORRENT_URL: 1,
            DIRECT_URL: 2,
            INFOHASH: 3,
            PLAYER_ID: 4,
            TORRENT_RAW: 5
        },
        
        BGP_STATE = {
            IDLE: 0,
            PREBUFFERING: 1,
            DOWNLOADING: 2,
            BUFFERING: 3,
            COMPLETED: 4,
            HASHCHECKING: 5,
            ERROR: 6,
            CONNECTING: 7,
            LOADING: 8
        },
        BGP_STATE_NAMES = ['idle', 'prebuf', 'dl', 'buf', 'completed', 'check', 'error', 'connecting', 'loading'],

        PLUGIN_STATE = {
            IDLE: 0,
            OPENING: 1,
            BUFFERING: 2,
            PLAYING: 3,
            PAUSED: 4,
            STOPPING: 5,
            STOPPED: 6,
            ERROR: 7
        },
        PLUGIN_STATE_NAMES = ['idle', 'opening', 'buffering', 'playing', 'paused', 'stopping', 'stopped', 'error'],

        MEDIA_STATE = {
            LOADING: 0,
            IDLE: 1,
            HASHCHECKING: 2,
            PREBUFFERING: 3,
            BUFFERING: 4,
            PLAYING: 5,
            PAUSED: 6,
            STOPPED: 7,
            ERROR: 8,
            CONNECTING: 9
        },
        MEDIA_STATE_NAMES = ['loading', 'idle', 'check', 'prebuf', 'buf', 'play', 'pause', 'stop', 'error', 'connecting'],
    
    _lastMessage = null,
    _lastMessageType = null,
    
    // custom vars associated with player
    vars = {},
    
    triggers = {
        skipMediaState: true,
        lockBgProcessState: true,
        mediaStarted: false,
        stopClicked: false,
        nextClicked: false,
        prevClicked: false,
        skipEngineStatus: 0
    },
    
    status = {
        
        mediaState: MEDIA_STATE.CONNECTING,
        
        video: {
            subtitle: {
                count: 0,
                current: -1,
                values: []
            },
            aspect_ratio: {
                current: 0,
                values: ["default", "1:1", "4:3", "16:9", "16:10", "221:100", "5:4"]
            },
            crop: {
                current: 0,
                values: ["default", "16:9", "16:10", "185:100", "239:100", "5:3", "4:3", "5:4", "1:1"]
            }
        },
        audio: {
            track: {
                count: 0,
                current: -1,
                values: []
            },
            channel: {
                current: 0,
                values: ["default", "stereo", "reverseStereo", "left", "right", "dolby"]
            }
        }
        
    },
    
    pluginData = {
        inputState: PLUGIN_STATE.IDLE,
        time: 0,
        duration: 0,
        progress: 0,
        volume: 0,
        muted: false,
        version: VER_1_0_2,
        stringVersion: "1.0.2",
        countPlaylistItems: 0,
        isAd: false,
        isInterruptableAd: false,
        qt: false,
        type: 0,
        events: false
    },
    
    bgProcessData = {
        state: BGP_STATE.CONNECTING
    },
    
    timers = {
        updateState: null,
        waitPrebuffering: null,
        iframeChild: null,
        preloadContent: null,
        loadData: null
    },
    
    _playlist = null;
    
    ////////////////////////////////////////////////////////////////////////////
    // private functions
    function getVersion(stringVersion)
    {
        var a = stringVersion.split(".");
        if(a.length != 3 && a.length != 4) {
            throw "Bad version: " + stringVersion;
        }

        return (parseInt(a[0]) * 10000 + parseInt(a[1]) * 100 + parseInt(a[2]));
    }
    
    function embedPlugin(container, useInternalPlaylist, style, bgColor, fontColor, callback)
    {
        _log("embedPlugin: container=" + container);

        var internalPlaylist = useInternalPlaylist ? "true" : "false",
            embed,
            document = container.ownerDocument;
        
        if(_browser.name == "ie") {
            
            // 1: Torrent Stream P2P Multimedia Plug-in, FAA285EB-EB55-47ff-84FF-0993CA2A41B5
            // 2: ACE Stream P2P Multimedia Plug-in, 79690976-ED6E-403c-BBBA-F8928B5EDE17
            // 3: Torrent Stream P2P Multimedia Plug-in 2, 28E3B95D-371D-42d5-A276-8A3EE70100FD
            
            var html = "", clsid;
            
            if(pluginData.type == 1) {
                clsid = "FAA285EB-EB55-47ff-84FF-0993CA2A41B5";
            }
            else if(pluginData.type == 2) {
                clsid = "79690976-ED6E-403c-BBBA-F8928B5EDE17";
            }
            else if(pluginData.type == 3) {
                clsid = "28E3B95D-371D-42d5-A276-8A3EE70100FD";
            }
            else {
                _log("embedPlugin: unknown type: " + pluginData.type);
                return false;
            }
            
            html += '<object classid="clsid:' + clsid + '" width="50%" height="100%">';
            html += '<param name="autoplay" value="0" />';
            html += '<param name="loop" value="0" />';
            html += '<param name="bgcolor" value="' + bgColor + '" />';
            html += '<param name="video-bgcolor" value="' + bgColor + '" />';
            html += '<param name="fontcolor" value="' + fontColor + '" />';
            html += '<param name="internalplaylist" value="' + internalPlaylist + '" />';
            
            if(pluginData.type == 1) {
                html += '<param name="fullscreencontrols" value="false" />';
            }
            else {
                html += '<param name="fullscreencontrols" value="true" />';
                html += '<param name="fscontrolsenable" value="1" />';
                if(conf.useInternalControls) {
                    html += '<param name="nofscontrolsenable" value="1" />';
                }
                else {
                    html += '<param name="nofscontrolsenable" value="0" />';
                }
                if(conf.liveStreamControls) {
                    html += '<param name="defaultcontrolsforstream" value="1" />';
                }
                else {
                    html += '<param name="defaultcontrolsforstream" value="0" />';
                }
                html += '<param name="fscontrols" value="default" />';
                html += '<param name="nofscontrols" value="default" />';
                html += '<param name="nofscontrolsheight" value="36" />';
            }
            html += '</object>';
            
            container.innerHTML = html;
            embed = container.firstChild;
        
            embed.setAttribute("width", "100%");
            embed.setAttribute("height", "100%");
            embed.style.width = "100%";
            embed.style.height = "100%";
        }
        else {
            // clear container
            while(container.firstChild)
            {
                container.removeChild(container.firstChild);
            }
            
            embed = document.createElement("embed");
            if(pluginData.qt) {
                if(pluginData.type == 1) {
                    embed.setAttribute("type", "application/x-tstream");
                }
                else if(pluginData.type == 2) {
                    embed.setAttribute("type", "application/x-acestream-plugin");
                }
                else if(pluginData.type == 4) {
                    embed.setAttribute("type", "application/x-tstream");
                }
                else if(pluginData.type == 3) {
                    embed.setAttribute("type", "application/x-torrentstream-plugin");
                }
                else {
                    _log("embedPlugin: unknown type: " + pluginData.type);
                    return false;
                }
                embed.setAttribute("width", "100%");
                embed.setAttribute("height", "100%");
                embed.setAttribute("bgcolor", bgColor);
                embed.setAttribute("videobgcolor", bgColor);
                embed.setAttribute("fontcolor", fontColor);
                embed.setAttribute("fullscreencontrols", "1");
                embed.setAttribute("fscontrolsenable", "1");
                if(conf.useInternalControls) {
                    embed.setAttribute("nofscontrolsenable", "1");
                }
                else {
                    embed.setAttribute("nofscontrolsenable", "0");
                }
                if(conf.liveStreamControls) {
                    embed.setAttribute("defaultcontrolsforstream", "1");
                }
                else {
                    embed.setAttribute("defaultcontrolsforstream", "0");
                }
                embed.setAttribute("loopable", "0");
                embed.setAttribute("fscontrols", "default");
                embed.setAttribute("nofscontrols", "default");
                embed.setAttribute("nofscontrolsheight", "36");
            }
            else {
                embed.setAttribute("type", "application/x-ts-stream");
                embed.setAttribute("internalplaylist", internalPlaylist);
                embed.setAttribute("autoplay", "no");
                embed.setAttribute("loop", "no");
                embed.setAttribute("width", "100%");
                embed.setAttribute("height", "100%");
                embed.setAttribute("bgcolor", bgColor);
                embed.setAttribute("video-bgcolor", bgColor);
                embed.setAttribute("fontcolor", fontColor);
            }
            
            if(style) {
                embed.setAttribute("style", style);
            }
            
            container.appendChild(embed);
        }
        
        try {
            embed.width = "100%";
            embed.height = "100%";
        }
        catch(e) {
        }

        embed.style.width = "100%";
        embed.style.height = "100%";
        
        if(conf.firefoxUnwrapEmbedObjects) {
            try {
                // works in FF >= 3.6.2
                embed = XPCNativeWrapper.unwrap(embed);
            }
            catch(e) {
                try {
                    embed = embed.wrappedJSObject;
                }
                catch(e) {
                    _log("embedPlugin: cannot unwrap plugin object: " + e);
                }
            }
        }
        
        var checkProp;
        if(pluginData.qt) {
            checkProp = "state";
        }
        else {
            checkProp = "input";
        }
        _log("embedPlugin: wrap=" + conf.firefoxUnwrapEmbedObjects + " embed=" + embed);
        
        if(typeof(embed[checkProp]) === 'undefined') {
            
            if(typeof callback === "function") {
                // asynchronous embed
                
                function checkPlugin(retries) {
                    _log("embedPlugin:async: retries=" + retries + " embed[" + checkProp + "]=" + typeof embed[checkProp]);
                    
                    if(typeof(embed[checkProp]) != 'undefined') {
                        // got plugin
                        callback.call(self, embed);
                        return;
                    }
                    
                    if(retries <= 50) {
                        setTimeout(function() {
                                checkPlugin(retries + 1);
                        }, 100);
                    }
                    else {
                        // out of tries, failed
                        callback.call(self, false);
                    }
                }
                
                checkPlugin(0);
            }
            
            _log("embedPlugin: failed to init: typeof embed[" + checkProp + "]=" + typeof embed[checkProp]);
            _log("embedPlugin: failed to init: embed[" + checkProp + "]=" + embed[checkProp]);
            
            return false;
        }
		else {
			if(typeof callback === "function") {
				callback.call(self, embed);
			}
		}

        return embed;
    }
    
    function _authLevel()
    {
        if(pluginData.version == VER_1_0_2) {
            return 0;
        }
        else {
            try {
                if(pluginData.qt) {
                    _log("authLevel: " + content.auth);
                    return content.auth ? 1 : 0;
                }
                else {
                    return content.input.tsAuth;
                }
            }
            catch(e) {
                _log("tsAuth error: " + e);
                return 0;
            }
        }
    }
    
    function ts_info()
    {
        if(!content) {
            return "";
        }
        
        var info = "";
        try {
            if(pluginData.qt) {
                info = content.status;
            }
            else if(pluginData.version == VER_1_0_2) {
                info = content.input.p2pstatus || "";
            }
            else {
                info = content.input.tsInfo || "";
            }
        }
        catch(e){
            _log("tsInfo error: " + e);
        }
        
        return info;
    }
    
    function parseBgprocessStatus(statusString)
    {
        var a, offset, main, ad, status = {};
        
        if(!statusString) {
            return null;
        }
        
        a = statusString.split("|");
        if(a.length == 1) {
            main = a[0];
        }
        else if(a.length == 2) {
            main = a[0];
            ad = a[1];
        }
        else {
            return null;
        }
        
        if(main.substring(0, 5) !== "main:") {
            return null;
        }
        
        main = main.substring(5);
        a = main.split(";");
        status.status = a[0];
        
        if(status.status === "err") {
            status.errorMessage = a[2];
        }
        else if(status.status === "check") {
            status.progress = a[1];
        }
        else if(status.status === "prebuf") {
            status.progress = a[1];
            status.time = a[2];
            offset = 3;
        }
        else if(status.status === "buf") {
            status.progress = a[1];
            status.time = a[2];
            offset = 3;
        }
        else if(status.status === "wait") {
            status.time = a[1];
            offset = 2;
        }
        else if(status.status === "dl") {
            offset = 1;
        }
        
        if(status.status !== "idle" && status.status !== "err" && status.status !== "check") {
            try {
                status.totalProgress = parseInt(a[offset]);
                status.immediateProgress = parseInt(a[offset+1]);
                status.speedDown = parseInt(a[offset+2]);
                status.httpSpeedDown = parseInt(a[offset+3]);
                status.speedUp = parseInt(a[offset+4]);
                status.peers = parseInt(a[offset+5]);
                status.httpPeers = parseInt(a[offset+6]);
                status.downloaded = parseInt(a[offset+7]);
                status.httpDownloaded = parseInt(a[offset+8]);
                status.uploaded = parseInt(a[offset+9]);
                
                if(a.length >= offset+9) {
                    status.liveData = a[offset+10];
                }
            }
            catch(e) {
                _log("parseBgprocessStatus:exc: " + e);
            }
        }
        
        return status;
    }
    
    function ts_status()
    {
        if(!content) {
            return 0;
        }
        
        var status = 0;
        try {
            if(pluginData.qt) {
                status = content.state;
            }
            else if(pluginData.version > VER_1_0_2) {
                status = content.input.tsStatus || 0;
            }
        }
        catch(e){
            _log("ts_status:exc: " + e);
        }
        
        return status;
    } //}}}
    //{{{ ts_error
    function ts_error()
    {
        if(!(content)) {
            return "";
        }
        
        var errmsg = "";
        try {
            if(pluginData.qt) {
                errmsg = content.error;
            }
            else if(pluginData.version > VER_1_0_2) {
                errmsg = content.input.tsError;
            }
        }
        catch(e){}
        
        return errmsg;
    } //}}}
    
    function attachPluginEvents()
    {
        content.stateChanged = function(state) {
            _log("event:stateChanged: state=" + state);
            var prevState = bgProcessData.state; 
            bgProcessData.state = state;
            
            if(status.mediaState == MEDIA_STATE.CONNECTING) {
                if(bgProcessData.state != BGP_STATE.CONNECTING) {
                    _log("event:stateChanged: bg connected");
                    status.mediaState = MEDIA_STATE.IDLE;
                    onConnected();
                }
                return;
            }
            
            if(status.mediaState == MEDIA_STATE.LOADING) {
                if(bgProcessData.state != BGP_STATE.LOADING) {
                    _log("event:stateChanged: playlist loaded");
                    status.mediaState = MEDIA_STATE.IDLE;
                    onPlaylistLoaded();
                }
                return;
            }
            
            var currentItem = self.playlistCurrentItem();
            if(state == BGP_STATE.IDLE) {
                status.mediaState = MEDIA_STATE.IDLE;
                if(prevState == BGP_STATE.HASHCHECKING
                    || prevState == BGP_STATE.PREBUFFERING
                    || prevState == BGP_STATE.BUFFERING
                    || prevState == BGP_STATE.DOWNLOADING
                    ) {
                    onStop(true, self.playlistCurrentItem());
                    }
            }
            else if(state == BGP_STATE.LOADING) {
                status.mediaState = MEDIA_STATE.LOADING;
            }
            else if(state == BGP_STATE.HASHCHECKING) {
                status.mediaState = MEDIA_STATE.HASHCHECKING;
                onChecking(currentItem);
            }
            else if(state == BGP_STATE.PREBUFFERING) {
                status.mediaState = MEDIA_STATE.PREBUFFERING;
                onPrebuffering(currentItem);
            }
            else if(state == BGP_STATE.BUFFERING) {
                status.mediaState = MEDIA_STATE.BUFFERING;
                onBuffering(currentItem);
            }
            else if(state == BGP_STATE.ERROR) {
                status.mediaState = MEDIA_STATE.ERROR;
            }
            else if(state == BGP_STATE.DOWNLOADING) {
                if(prevState == BGP_STATE.BUFFERING) {
                    status.mediaState = MEDIA_STATE.PLAYING;
                    if(triggers.mediaStarted) {
                        onResume(currentItem);
                    }
                    else {
                        onStart(currentItem);
                    }
                }
            }
        };
        
        content.playlistChanged = function() {
            _log("event:playlistChanged: current=" + content.playlistCurrentItem + " count=" + content.playlistCount);
            
            bgProcessData.state = content.state;
            if(status.mediaState == MEDIA_STATE.LOADING) {
                if(bgProcessData.state != BGP_STATE.LOADING) {
                    _log("event:playlistChanged: playlist loaded");
                    status.mediaState = MEDIA_STATE.IDLE;
                    onPlaylistLoaded();
                }
                return;
            }
            
            var currentItem = content.playlistCurrentItem;
            if(currentItem != _lastStartedItem) {
                _log("event:playlistChanged: changed item: last=" + _lastStartedItem + " curr=" + currentItem);
                if(_lastStartedItem != -1) {
                    onStop(false, _lastStartedItem);
                    status.mediaState = MEDIA_STATE.STOPPED;
                }
                _lastStartedItem = currentItem;
            }
        };
        
        content.audioMuteChanged = function(/*bool*/mute) {
            _log("event:audioMuteChanged: mute=" + mute);
            onMute(mute);
        };
        
        content.audioVolumeChanged = function(/*int*/volume) {
            _log("event:audioVolumeChanged: volume=" + volume);
            volume = Math.round(volume / 2);
            onVolume(volume);
        };
        
        content.audioTrackChanged = function(/*int*/track) {
            _log("event:audioTrackChanged: ---");
        };
        
        content.audioChannelChanged = function(/*int*/channel) {
            _log("event:audioChannelChanged: ---");
        };
        
        content.inputPositionChanged = function(/*double*/position) {
            _log("event:inputPositionChanged: position=" + position);
            onProgress(position);
        };
        
        content.inputTimeChanged = function(/*double*/time) {
            _log("event:inputTimeChanged: time=" + time);
            time = parseInt(time / 1000);
            onTime(time);
        };
        
        content.inputRateChanged = function(/*double*/rate) {
            _log("event:inputRateChanged: ---");
        };
        
        content.subtitleTrackChanged = function(/*int*/track) {
            _log("event:subtitleTrackChanged: ---");
        };
        
        content.videoFullscreenChanged = function(/*bool*/isfullscreen) {
            _log("event:videoFullscreenChanged: ---");
        };
        
        content.videoAspectRatioChanged = function(/*string*/aspectRatio) {
            _log("event:videoAspectRatioChanged: aspectRatio=" + aspectRatio);
        };
        
        content.videoCropChanged = function(/*string*/crop) {
            _log("event:videoCropChanged: ---");
        };
        
        content.authChanged = function(/*bool*/auth) {
            _log("event:authChanged: ---");
        };
        
        content.infoChanged = function(/*string*/unparsedinfo) {
            _log("event:infoChanged: ---");
        };
        
        content.errorChanged = function(/*string*/error) {
            _log("event:errorChanged: error=" + error);
            onError(error);
        };
        
        content.statusChanged = function(/*string*/unparsedstatus) {
            //_log("event:statusChanged: ---");
            try {
                var bgprocessStatus = parseBgprocessStatus(unparsedstatus)
                if(bgprocessStatus) {
                    onEvent("onStatus", bgprocessStatus);
                }
            }
            catch(e) {
            }
        };
        
        content.mediaPlayerMediaChanged = function() {
            _log("event:mediaPlayerMediaChanged: ---");
        };
        
        content.mediaPlayerNothingSpecial = function() {
            _log("event:mediaPlayerNothingSpecial: ---");
        };
        
        content.mediaPlayerOpening = function() {
            _log("event:mediaPlayerOpening: ---");
        };
        
        content.mediaPlayerBuffering = function() {
            _log("event:mediaPlayerBuffering: ---");
        };
        
        content.mediaPlayerPlaying = function() {
            _log("event:mediaPlayerPlaying: ---");
            status.mediaState = MEDIA_STATE.PLAYING;
            var currentItem = self.playlistCurrentItem();
            if(triggers.mediaStarted) {
                onResume(currentItem);
            }
            else {
                onStart(currentItem);
            }
        };
        
        content.mediaPlayerPaused = function() {
            _log("event:mediaPlayerPaused: ---");
            status.mediaState = MEDIA_STATE.PAUSED;
            onPause(self.playlistCurrentItem());
        };
        
        content.mediaPlayerStopped = function() {
            _log("event:mediaPlayerStopped: ---");
            
            if(content.state == BGP_STATE.IDLE) {
                status.mediaState = MEDIA_STATE.IDLE;
            }
            else {
                status.mediaState = MEDIA_STATE.STOPPED;
            }
            onStop(status.mediaState == MEDIA_STATE.IDLE, self.playlistCurrentItem());
        };
        
        content.mediaPlayerForward = function() {
            _log("event:mediaPlayerForward: ---");
        };
        
        content.mediaPlayerBackward = function() {
            _log("event:mediaPlayerBackward: ---");
        };
        
        content.mediaPlayerEndReached = function() {
            _log("event:mediaPlayerEndReached: ---");
        };
        
        content.mediaPlayerEncounteredError = function() {
            _log("event:mediaPlayerEncounteredError: ---");
        };
        
        content.mediaPlayerTimeChanged = function(/*string*/formattedTime) {
            //_log("event:mediaPlayerTimeChanged: formattedTime=" + formattedTime);
            onTime(formattedTime, true);
        };
        
        content.mediaPlayerPositionChanged = function(/*double*/position) {
            //_log("event:mediaPlayerPositionChanged: position=" + position);
            onProgress(position);
        };
        
        content.mediaPlayerSeekableChanged = function() {
            _log("event:mediaPlayerSeekableChanged: ---");
        };
        
        content.mediaPlayerPausableChanged = function() {
            _log("event:mediaPlayerPausableChanged: ---");
        };
        
        content.mediaPlayerTitleChanged = function() {
            _log("event:mediaPlayerTitleChanged: ---");
        };
        
        content.mediaPlayerSnapshotTaken = function() {
            _log("event:mediaPlayerSnapshotTaken: ---");
        };
        
        content.mediaPlayerLengthChanged = function(/*string*/formatedlength) {
            _log("event:mediaPlayerLengthChanged: ---");
        };
        
        content.errorMessage = function(/*string*/message) {
            _log("event:errorMessage: message=" + message);
        };
    }
    
    //{{{ updateState
    function updateState()
    {
        if(!content) {
            return;
        }
        
        try {
            updateBGProcessData();
        }
        catch(e) {
            _log("updateState: updateBGProcessData exc: " + e);
        }
        
        try {
            updatePluginData();
        }
        catch(e) {
            _log("updateState: updatePluginData exc: " + e);
        }
        
        try {
            updateMediaState();
        }
        catch(e) {
            _log("updateState: updateMediaState exc: " + e);
        }
        
        timers.updateState = setTimeout(updateState, 100);
    }
    
    function updateBGProcessData()
    {
        if(triggers.skipEngineStatus > 0) {
            triggers.skipEngineStatus -= 1;
            return;
        }
        
        var newState = ts_status();
        if(newState == -1) {
            return;
        }
        
        if(newState !== bgProcessData.state) {
            if(!conf.useInternalPlaylist && bgProcessData.state == BGP_STATE.PREBUFFERING && newState == BGP_STATE.DOWNLOADING) {
                _log("updateBGProcessData: unlock bgprocess state");
                triggers.lockBgProcessState = false;
            }
            
            if(!triggers.lockBgProcessState) {
                _log("updateBGProcessData: state change: " + BGP_STATE_NAMES[bgProcessData.state] + " -> " + BGP_STATE_NAMES[newState]);
                bgProcessData.state = newState;
            }
        }
        
        if(status.mediaState != MEDIA_STATE.IDLE && status.mediaState != MEDIA_STATE.STOPPED) {
            if(_bgprocessStatusCounter >= 10) {
                var bgprocessInfo = ts_info();
                
                if(pluginData.version < VER_1_0_5) {
                    onMessage("message", bgprocessInfo);
                }
                else {
                    var bgprocessStatus = parseBgprocessStatus(bgprocessInfo)
                    if(bgprocessStatus) {
                        onEvent("onStatus", bgprocessStatus);
                    }
                }
                _bgprocessStatusCounter = 0;
            }
            else {
                _bgprocessStatusCounter += 1;
            }
        }
        
    }
    
    function updatePluginData()
    {
        if(!content) {
            return;
        }
        
        // input.state
        var newState;
        try {
            if(pluginData.qt) {
                newState = content.inputState;
            }
            else {
                newState = content.input.state;
            }
        }
        catch(e) {
            newState = PLUGIN_STATE.ERROR;
        }
        
        if(pluginData.inputState != newState) {
            _log("updatePluginData: state change: " + PLUGIN_STATE_NAMES[pluginData.inputState] + " -> " + PLUGIN_STATE_NAMES[newState] + " triggers.mediaStarted=" + triggers.mediaStarted);
            pluginData.inputState = newState;
        }
        
        /*
        if(pluginData.version >= VER_1_0_4) {
            try {
                pluginData.isAd = content.input.isAd;
                if(pluginData.version >= VER_1_0_5) {
                    pluginData.isInterruptableAd = content.input.isInterruptableAd;
                }
            }
            catch(e) {}
        }
        */
        
        if( ! triggers.mediaStarted) {
            return;
        }
        
        if(pluginData.inputState == PLUGIN_STATE.PLAYING) {
            // media duration
            // Duration can be not available at start but it can appear
            // later (after vlc reads all neccessary data), so check it
            // periodically.
            if(pluginData.duration == 0) {
                try {
                    if(pluginData.qt) {
                        pluginData.duration = content.inputLength;
                    }
                    else {
                        pluginData.duration = content.input.length;
                    }
                    if(pluginData.duration != 0) {
                        _log("updatePluginData: got duration: " + pluginData.duration);
                        onDuration(pluginData.duration);
                    }
                }
                catch(e) {}
            }
            
            // playtime
            try {
                var time;
                if(pluginData.qt) {
                    time = content.inputTime;
                }
                else {
                    time = content.input.time;
                }
                
                time = parseInt(time / 1000);
                if(time != pluginData.time) { 
                    pluginData.time = time;
                    onTime(pluginData.time);
                }
            }
            catch(e) {}
            
            // position
            try {
                var position;
                if(pluginData.qt) {
                    position = content.inputPosition;
                }
                else {
                    position = content.input.position;
                }
                
                if(position != pluginData.progress) {
                    pluginData.progress = position;
                    onProgress(pluginData.progress);
                }
            }
            catch(e) {}
        }
        
        // volume
        try {
            var newVolume;
            if(pluginData.qt) {
                newVolume = content.audioVolume;
            }
            else {
                newVolume = content.audio.volume;
            }
            
            if(pluginData.version < VER_2_0_10) {
                newVolume = Math.round(newVolume / 2);
            }
            
            if(newVolume != pluginData.volume) {
                pluginData.volume = newVolume;
                onVolume(newVolume);
            }
        }
        catch(e) {
            pluginData.volume = 0;
        }
        
        // muted
        try {
            var newMuted;
            if(pluginData.qt) {
                newMuted = content.audioMute;
            }
            else {
                newMuted = content.audio.mute;
            }
            
            if(newMuted != pluginData.muted) {
                pluginData.muted = newMuted;
                onMute(newMuted);
            }
        }
        catch(e) {
        }
        
    } //}}}
    
    function updateMediaState()
    {
        var state = -1;
        
        if(status.mediaState == MEDIA_STATE.CONNECTING) {
            if(bgProcessData.state !== undefined && bgProcessData.state != BGP_STATE.CONNECTING) {
                _log("updateMediaState: bg connected");
                status.mediaState = MEDIA_STATE.IDLE;
                onConnected();
            }
            return;
        }
        
        if(status.mediaState == MEDIA_STATE.LOADING) {
            if(bgProcessData.state != BGP_STATE.LOADING) {
                _log("updateMediaState: playlist loaded");
                status.mediaState = MEDIA_STATE.IDLE;
                onPlaylistLoaded();
            }
            return;
        }
        
        if(triggers.skipMediaState) {
            return;
        }
        
        if(bgProcessData.state == BGP_STATE.LOADING) {
            state = MEDIA_STATE.LOADING;
        }
        else if(bgProcessData.state == BGP_STATE.PREBUFFERING) {
            state = MEDIA_STATE.PREBUFFERING;
        }
        else if(bgProcessData.state == BGP_STATE.HASHCHECKING) {
            state = MEDIA_STATE.HASHCHECKING;
        }
        else if(bgProcessData.state == BGP_STATE.BUFFERING) {
            state = MEDIA_STATE.BUFFERING;
        }
        else if(bgProcessData.state == BGP_STATE.ERROR) {
            state = MEDIA_STATE.ERROR;
        }
        else {
            // check plugin state
            if(pluginData.inputState == PLUGIN_STATE.IDLE) {
                if(bgProcessData.state == BGP_STATE.DOWNLOADING) {
                    // bgprocess finished prebuffering and sent PLAY to plugin
                    // but plugin haven't yet processed this command
                    state = MEDIA_STATE.PREBUFFERING;
                }
                else {
                    state = MEDIA_STATE.IDLE;
                }
            }
            else if(pluginData.inputState == PLUGIN_STATE.OPENING || pluginData.inputState == PLUGIN_STATE.BUFFERING) {
                state = MEDIA_STATE.BUFFERING;
            }
            else if(pluginData.inputState == PLUGIN_STATE.PLAYING) {
                state = MEDIA_STATE.PLAYING;
            }
            else if(pluginData.inputState == PLUGIN_STATE.PAUSED) {
                state = MEDIA_STATE.PAUSED;
            }
            else if(pluginData.inputState == PLUGIN_STATE.STOPPING || pluginData.inputState == PLUGIN_STATE.STOPPED) {
                if(bgProcessData.state == BGP_STATE.IDLE) {
                    state = MEDIA_STATE.IDLE;
                }
                else {
                    state = MEDIA_STATE.STOPPED;
                }
            }
            else if(pluginData.inputState == PLUGIN_STATE.ERROR) {
                state = MEDIA_STATE.ERROR;
            }
        }
        
        if(state == -1) {
            // cannot determine current media state, leave it untouched
            return;
        }
        
        if(state == MEDIA_STATE.LOADING) {
            status.mediaState = state;
            return;
        }
        
        var currentItem = self.playlistCurrentItem();
        if(currentItem != _lastStartedItem) {
            _log("updateMediaState: changed item: last=" + _lastStartedItem + " curr=" + currentItem);
            if(_lastStartedItem != -1) {
                onStop(false, _lastStartedItem);
                status.mediaState = MEDIA_STATE.STOPPED;
            }
            _lastStartedItem = currentItem;
        }
        
        if(state != status.mediaState) {
            _log("updateMediaState: " + MEDIA_STATE_NAMES[status.mediaState] + " -> " + MEDIA_STATE_NAMES[state] + " plugin=" + PLUGIN_STATE_NAMES[pluginData.inputState] + " bg=" + BGP_STATE_NAMES[bgProcessData.state] + " curr=" + currentItem);
            
            // generate pseudo-events
            if(state == MEDIA_STATE.PLAYING) {
                if(triggers.mediaStarted) {
                    onResume(currentItem);
                }
                else {
                    onStart(currentItem);
                }
            }
            else if(state == MEDIA_STATE.PAUSED) {
                if(status.mediaState == MEDIA_STATE.BUFFERING || status.mediaState == MEDIA_STATE.PLAYING) {
                    onPause(currentItem);
                }
                else {
                    _log("updateMediaState: unknown state change: " + status.mediaState + " -> " + state);
                }
            }
            else if(status.mediaState == MEDIA_STATE.IDLE && state == MEDIA_STATE.STOPPED) {
                // plugin is stopped, bgprocess is idle (after fullstop), but the content is downloaded
                // when restart playing after fullstop there won't be prebuffering (because content is downloaded)
                onPrebuffering(currentItem);
            }
            else if(state == MEDIA_STATE.STOPPED || state == MEDIA_STATE.IDLE) {
                if(status.mediaState == MEDIA_STATE.PREBUFFERING
                    || status.mediaState == MEDIA_STATE.BUFFERING
                    || status.mediaState == MEDIA_STATE.HASHCHECKING
                    || status.mediaState == MEDIA_STATE.PLAYING
                    || status.mediaState == MEDIA_STATE.PAUSED)
                {
                    onStop(state == MEDIA_STATE.IDLE, currentItem);
                }
                else if(status.mediaState == MEDIA_STATE.STOPPED && state == MEDIA_STATE.IDLE) {
                    onStop(true, currentItem);
                }
                else {
                    _log("updateMediaState: unknown state change: " + MEDIA_STATE_NAMES[status.mediaState] + " -> " + MEDIA_STATE_NAMES[state]);
                }
            }
            else if(state == MEDIA_STATE.PREBUFFERING) {
                onPrebuffering(currentItem);
            }
            else if(state == MEDIA_STATE.BUFFERING) {
                onBuffering(currentItem);
            }
            else if(state == MEDIA_STATE.HASHCHECKING) {
                onChecking(currentItem);
            }
            else if(state == MEDIA_STATE.ERROR) {
                onError();
            }
            
            status.mediaState = state;
        }
    }
    
    function onConnected() {
        _log("onConnected");
        if(typeof conf.onLoad === 'function') {
            conf.onLoad.call(self);
        }
    }
    
    function isVideoLoaded()
    {
        try {
            if(content && content.input) {
                _log("length=" + content.input.length);
                if(content.input.length != -1) {
                    return true;
                }
            }
        }
        catch(e) {}
        
        return false;
    }
    
    function waitPrebuffering()
    {
        if(content.playlist.items.count > 0) {
            _log("waitPrebuffering: ready to play");
            _log("waitPrebuffering: items_count=" + content.playlist.items.count);
            pluginData.countPlaylistItems = content.playlist.items.count;
            content.playlist.play();
            triggers.skipMediaState = false;
            content.audio.mute = pluginData.muted;
            _log("waitPrebuffering: mute=" + content.audio.mute);
            timers.waitPrebuffering = null;
        }
        else {
            timers.waitPrebuffering = setTimeout(waitPrebuffering, 100);
        }
    }
    
    function onEvent(event)
    {
        try {
            var args = Array.prototype.slice.call(arguments, 1);
            for(var i = 0; i < eventHandlers.length; i++) {
                if(typeof eventHandlers[i][event] === 'function') {
                    eventHandlers[i][event].apply(self, args);
                }
            }
        }
        catch(e) {
            _log("onEvent:exc: event=" + event + " err=" + e);
        }
    }
    
    function onMessage(type, msg) {
        
        if(!msg) {
            return;
        }
        
        if(type === "message" && (new RegExp('^error', 'i')).test(msg)) {
            type = "error";
        }
        
        if(type === "alert" || type !== _lastMessageType || msg !== _lastMessage) {
            _lastMessageType = type;
            _lastMessage = msg;
            onEvent("onMessage", type, msg);
        }
    }
    
    function onError(msg) {
        if(msg) {
            onMessage("error", msg);
        }
    }
    
    function onSystemMessage(msg) {
        onEvent("onSystemMessage", msg);
    }
    
    function onTime(time, formatted) {
        onEvent("onTime", self.playlistCurrentItem(), time, formatted);
    }
    
    function onProgress(progress) {
        onEvent("onProgress", self.playlistCurrentItem(), progress);
    }
    
    function onDuration(duration) {
        onEvent("onDuration", self.playlistCurrentItem(), duration);
    }
    
    function onPrebuffering(index)
    {
        onEvent("onPrebuffering", index);
    }
    
    function onBuffering(index)
    {
        onEvent("onBuffering", index);
    }
    
    function onChecking(index)
    {
        onEvent("onChecking", index);
    }
    
    function onStart(index) {
        _log("onStart: index=" + index);
        
        // now media data is available and can be read
        loadMediaData();
        
        if(pluginData.version < VER_1_0_5) {
            // restore media params from previous playlist item
            restoreMediaParams();
        }
        
        // mark content as started
        triggers.mediaStarted = true;
        _log("onStart: duration=" + pluginData.duration);
        
        onEvent("onStart", index);
    }
    
    function onPause(index) {
        onEvent("onPause", index);
    }
    
    function onResume(index) {
        onEvent("onResume", index);
    }
    
    function onStop(fullstop, index) {
        fullstop = !!fullstop;
        
        var currentProgress = -1,
            lastProgress = pluginData.progress;
            
        try {
            if(pluginData.qt) {
                currentProgress = content.inputPosition;
            }
            else {
                currentProgress = content.input.position;
            }
        }
        catch(e) {
        }
        _log("onStop: index=" + index + " fullstop=" + fullstop + " stopClicked=" + triggers.stopClicked + " currentProgress=" + currentProgress + " lastProgress=" + lastProgress);
        
        if(index === undefined) {
            index = self.playlistCurrentItem();
        }
        
        if(triggers.mediaStarted) {
            if(!conf.useInternalPlaylist) {
                // Reset aspectRation and crop to defaults on stop,
                // otherwise plugin hangs when start playing next item
                // Only parameters with string values affect this (audio
                // channel can be changed as well).
                try {
                    content.video.aspectRatio = "";
                    content.video.crop = "";
                }
                catch(e) {
                    _log("onStop: reset media params: " + e);
                }
            }
            
            // reset subtitles
            status.video.subtitle.current = -1;
            status.video.subtitle.values = [];
            status.video.subtitle.count = -1;
            
            // reset audio track
            status.audio.track.current = -1;
            status.audio.track.values = [];
            status.audio.track.count = -1;
            
            triggers.mediaStarted = false;
        }
        
        if(!conf.useInternalPlaylist) {
            status.mediaState = fullstop ? MEDIA_STATE.IDLE : MEDIA_STATE.STOPPED;
        }
        
        pluginData.duration = 0;
        pluginData.progress = 0;
        pluginData.time = 0;
        onTime(0);
        onProgress(0);
        _lastMessageType = null;
        _lastMessage = null;
        
        // send event
        onEvent("onStop", index, fullstop);
        
        if(!conf.useInternalPlaylist) {
            if( ! triggers.stopClicked) {
                // content finished playing
                onCompleted();
            }
            else {
                triggers.stopClicked = false;
                if(triggers.nextClicked) {
                    triggers.nextClicked = false;
                }
                if(triggers.prevClicked) {
                    triggers.prevClicked = false;
                }
            }
        }
        else if(conf.sendOnCompleted) {
            if(!fullstop && (currentProgress >= 0.97)) {
                onEvent("onCompleted", index);
            }
        }
    }
    
    function onCompleted()
    {
        _log("onCompleted");
        
        if(conf.useInternalPlaylist) {
            throw "Deprecated from v1.0.5";
        }
        
        if(_pluginIsAd()) {
            _log("onCompleted: ad completed, do nothing");
            return;
        }
        
        var next = self.playlistCurrentItem() + 1;
        if(next >= self.playlistSize()) {
            _log("onCompleted: no next item");
            
            try {
                if(content.video.fullscreen) {
                    content.video.fullscreen = false;
                }
                if(pluginData.version > VER_1_0_3) {
                    setTimeout(function() {
                            content.playlist.stop();
                    }, 1000);
                }
            }
            catch(e) {
                _log("onCompleted: " + e);
            }
            
            return;
        }
        
        _log("onCompleted: go to next item: " + next);
        _play(next, {}, true);
    }
    
    function onVolume(newVolume)
    {
        newVolume = Math.round(newVolume);
        onEvent("onVolume", newVolume);
    }
    
    function onMute(muted)
    {
        onEvent("onMute", muted);
    }
    
    function loadMediaData()
    {
        var i;
        
        // subtitle
        status.video.subtitle.current = -1;
        status.video.subtitle.values = [];
        status.video.subtitle.count = -1;
        
        try {
            if(pluginData.qt) {
                status.video.subtitle.current = content.subtitleTrack;
                status.video.subtitle.count = content.subtitleCount;
            }
            else if(content.subtitle) {
                status.video.subtitle.current = content.subtitle.track;
                status.video.subtitle.count = content.subtitle.count;
            }
            else {
                status.video.subtitle.current = content.video.subtitle;
            }
        }
        catch(e) {
            _log("loadMediaData: get subtitles: " + e);
        }
        _log("loadMediaData: subtitle current " + status.video.subtitle.current);
        _log("loadMediaData: subtitle count " + status.video.subtitle.count);
        
        if(status.video.subtitle.current != -1 && status.video.subtitle.count > 0) {
            var desc;
            for(i = 0; i < status.video.subtitle.count; i++) {
                if(pluginData.qt) {
                    desc = content.subtitleDescription(i);
                }
                else {
                    desc = content.subtitle.description(i);
                }
                status.video.subtitle.values.push(desc);
            }
        }
        
        // audio track
        status.audio.track.current = -1;
        status.audio.track.values = [];
        status.audio.track.count = -1;
        try {
            if(pluginData.qt) {
                status.audio.track.current = content.audioTrack;
                status.audio.track.count = content.audioCount;
            }
            else {
                status.audio.track.current = content.audio.track;
                if(content.audio.count !== undefined) {
                    status.audio.track.count = content.audio.count;
                }
            }
        }
        catch(e) {
            _log("loadMediaData: get audio track: " + e);
        }
        _log("loadMediaData: audio track current " + status.audio.track.current);
        _log("loadMediaData: audio track count " + status.audio.track.count);
        
        if(status.audio.track.current != -1 && status.audio.track.count > 0) {
            var desc;
            for(i = 0; i < status.audio.track.count; i++) {
                if(pluginData.qt) {
                    desc = content.audioDescription(i);
                }
                else {
                    desc = content.audio.description(i);
                }
                status.audio.track.values.push(desc);
            }
        }
    }
    
    function restoreMediaParams()
    {
        if(conf.useInternalPlaylist) {
            throw "Deprecated from v1.0.5";
        }
        
        _log("restoreMediaParams: aspectRatio=" + status.video.aspect_ratio.values[status.video.aspect_ratio.current]);
        _log("restoreMediaParams: crop=" + status.video.crop.values[status.video.crop.current]);
        self.aspectRatio(status.video.aspect_ratio.current);
        self.crop(status.video.crop.current);
    }
    
    function onPlaylist(files)
    {
        if(!conf.useInternalPlaylist) {
            
            // reset
            _playlist.data = {};
            _playlist.items = [];
            _playlist.currentItem = -1;
            
            var i = 0, tmp = [];
            for(i = 0; i < files.length; i++) {
                if(typeof(files[i]) == 'string') {
                    fileindex = i;
                    filename = files[i];
                }
                else {
                    filename = files[i][0];
                    fileindex = files[i][1];
                }
                tmp.push({
                        index: fileindex,
                        name: filename
                });
                _playlist.data[fileindex] = filename;
            }
            
            // sort
            tmp.sort(function(a, b) {
                    if(a.file < b.file) {
                        return -1;
                    }
                    else if(a.file == b.file) {
                        return 0;
                    }
                    else {
                        return 1;
                    }
            });
            
            // sorted array of filenames
            files = [];
            for(i = 0; i < tmp.length; i++) {
                files.push(tmp[i].name);
                _playlist.items.push({
                        index: tmp[i].index,
                        enabled: true
                });
            }
        }
        
        onEvent("onPlaylist", files);
    }
    
    function _checkPlugin() {
        if(pluginData.qt) {
            if(!content || typeof content.state === 'undefined') {
                throw "plugin is not initialised";
            }
        }
        else {
            if(!content || !content.input || !content.playlist) {
                throw "plugin is not initialised";
            }
        }
    }
    
    function loadPlaylist(mediaData)
    {
        _checkPlugin();
        
        if(_mediaData) {
            onPlaylist([]);
        }
        _mediaData = null;
        
        if(!content) {
            throw "Cannot embed plugin";
        }
        
        if(typeof(mediaData) !== 'object') {
            throw "loadPlaylist: mediaData is not an object";
        }
        
        if(mediaData.type === undefined) {
            throw "loadPlaylist: missing mediaData.type";
        }
        if(mediaData.id === undefined) {
            throw "loadPlaylist: missing mediaData.id";
        }
        
        var defaultSettings = {
            developerId: 0,
            affiliateId: 0,
            zoneId: 0,
            autoplay: false,
            name: null,
            identityUrl: null,
            async: true,
            clearPlaylist: false
        }
        mediaData = TorrentStream.Utils.extend(defaultSettings, mediaData);
        
        if(pluginData.version < VER_1_0_5 || !conf.useInternalPlaylist || mediaData.type == MEDIA_TYPE.DIRECT_URL) {
            mediaData.async = false;
        }
        
        _log("loadPlaylist: type=" + mediaData.type + " id=" + mediaData.id + " autoplay=" + mediaData.autoplay + " name=" + mediaData.name + " async=" + mediaData.async);
        
        try {
            var playlistData, loadResponse;
            
            if(mediaData.type == MEDIA_TYPE.TORRENT_URL) {
                if(pluginData.qt) {
                    if(mediaData.async) {
                        content.playlistLoadAsync(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                    else {
                        loadResponse = content.playlistLoad(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                }
                else {
                    if(mediaData.async) {
                        content.playlist.loadasync(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                    else {
                        loadResponse = content.playlist.load(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                }
            }
            else if(mediaData.type == MEDIA_TYPE.TORRENT_RAW) {
                if(pluginData.qt) {
                    if(mediaData.async) {
                        content.playlistLoadAsyncRaw(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                    else {
                        loadResponse = content.playlistLoadRaw(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                }
                else {
                    if(mediaData.async) {
                        content.playlist.loadasync_raw(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                    else {
                        loadResponse = content.playlist.load_raw(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                }
            }
            else if(mediaData.type == MEDIA_TYPE.INFOHASH) {
                if(pluginData.qt) {
                    if(mediaData.async) {
                        content.playlistLoadAsyncInfohash(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                    else {
                        loadResponse = content.playlistLoadInfohash(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                }
                else {
                    if(mediaData.async) {
                        content.playlist.loadasync_infohash(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                    else {
                        loadResponse = content.playlist.load_infohash(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId);
                    }
                }
            }
            else if(mediaData.type == MEDIA_TYPE.PLAYER_ID) {
                if(pluginData.qt) {
                    if(mediaData.async) {
                        content.playlistLoadAsyncPlayer(mediaData.id);
                    }
                    else {
                        loadResponse = content.playlistLoadPlayer(mediaData.id);
                    }
                }
                else {
                    if(pluginData.version < VER_1_0_5) {
                        loadResponse = content.playlist.load('http://storage.torrentstream.net/get/' + mediaData.id);
                    }
                    else {
                        if(mediaData.async) {
                            content.playlist.loadasync_player(mediaData.id);
                        }
                        else {
                            loadResponse = content.playlist.load_player(mediaData.id);
                        }
                    }
                }
            }
            else if(mediaData.type == MEDIA_TYPE.DIRECT_URL) {
                var name = mediaData.name || "";
                
                if(pluginData.qt) {
                    if(mediaData.clearPlaylist) {
                        content.playlistClear();
                    }
                    content.playlistLoadUrl(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId, name, mediaData.clearPlaylist);
                }
                else {
                    content.playlist.load_url(mediaData.id, mediaData.developerId, mediaData.affiliateId, mediaData.zoneId, name);
                }
                
                if(!name) {
                    name = mediaData.id;
                }
                playlistData = {
                    files: [name]
                }
            }
            else {
                throw "loadPlaylist: unknown media type: " + mediaData.type;
            }
            
            if(!conf.useInternalPlaylist) {
                // playlist is managed in javascript
                if(!playlistData) {
                    if(pluginData.version < VER_1_0_4) {
                        loadResponse = loadResponse.replace(/\\/g, "\\\\");
                    }
                    _log("loadPlaylist: loadResponse=" + loadResponse);
                    playlistData = TorrentStream.Utils.JSON.parse(loadResponse);
                    _log("loadPlaylist: playlistData=" + playlistData);
                }
            }
            
            _lastMediaData = mediaData;
            if(!mediaData.async) {
                onPlaylistLoaded(playlistData);
            }
            else {
                triggers.skipEngineStatus = 1;
                if(bgProcessData.state != BGP_STATE.LOADING) {
                    bgProcessData.state = BGP_STATE.LOADING;
                }
                if(status.mediaState != MEDIA_STATE.LOADING) {
                    status.mediaState = MEDIA_STATE.LOADING;
                }
            }
        }
        catch(e) {
            var failed = true;
            
            _log("loadPlaylist: cannot load content files: " + e + ", autoRetry=" + conf.autoRetry + " countRetry=" + conf.countRetry);
            if(conf.autoRetry) {
                if(conf.countRetry === undefined) {
                    conf.countRetry = 0;
                }
                if(conf.countRetry < 5) {
                    // don't show error, try again
                    failed = false;
                    timers.loadData = setTimeout(function() {
                            mediaData.countRetry += 1; 
                            loadPlaylist(mediaData);
                    }, 2500);
                }
                else {
                    _log("loadPlaylist: giving up");
                }
            }
            
            if(failed) {
                if(status.mediaState == MEDIA_STATE.LOADING) {
                    status.mediaState = MEDIA_STATE.ERROR;
                }
                onMessage("error", "cannotLoadPlaylist");
            }
            
            return false;
        }
    }
    
    function onPlaylistLoaded(playlistData) {
        
        if(!_lastMediaData) {
            throw "_lastMediaData is not initialised";
        }
        
        var files, infohash;
        
        if(!conf.useInternalPlaylist) {
            files = playlistData.files;
            infohash = playlistData.infohash;
        }
        else {
            try {
                var itemCount, i, name, files;
                
                files = [];
                itemCount = self.playlistSize();

                for(i = 0; i < itemCount; i++) {
                    if(i == 0) {
                        try {
                            // this method raises an exception when infohash is empty
                            if(pluginData.qt) {
                                infohash = content.playlistItemInfohash(i);
                            }
                            else {
                                infohash = content.playlist.ts_get_item_infohash(i);
                            }
                        }
                        catch(e) {
                            infohash = null;
                        }
                    }
                    
                    if(pluginData.qt) {
                        itemName = content.playlistItemTitle(i);
                    }
                    else {
                        itemName = content.playlist.ts_get_item_title(i);
                    }
                    
                    files.push(itemName);
                }
                _log("onPlaylistLoaded: files=" + ("" + files).replace(new RegExp(",", "g"), ", "));
            }
            catch(e) {
                _log("onPlaylistLoaded: exc: " + e);
            }
        }
        
        if(infohash) {
            _lastMediaData.infohash = infohash;
        }
        else {
            _lastMediaData.infohash = null;
        }
        _log("onPlaylistLoaded: infohash=" + _lastMediaData.infohash);
        
        if(files && files.length) {
            try {
                onPlaylist(files);
            }
            catch(e) {
                _log("onPlaylistLoaded: exc in onPlaylist(): " + e);
            }
            
            onMediaLoaded(_lastMediaData);
        }
        else {
            onMessage("error", "noVideoFiles");
        }
    }
    
    function preloadContent(index)
    {
        if(conf.useInternalPlaylist) {
            throw "preloadContent() is deprecated from 1.0.5";
        }
        
        _log("preloadContent: index=" + index);
        onPrebuffering(index);
        triggers.lockBgProcessState = true;
        bgProcessData.state = BGP_STATE.PREBUFFERING;
        status.mediaState = MEDIA_STATE.PREBUFFERING;
        
        if(content.playlist.items.count) {
            _log("preloadContent: clear current playlist");
            content.playlist.clear();
        }
        
        _log("preloadContent: index=" + index);
        var i, a = [];
        for(i = index; i < self.playlistSize(); i++) {
            if(_playlist.items[i].enabled) {
                a.push(_playlist.items[i].index);
            }
        }
        _log("preloadContent: start: " + a);
        var indexes = a.join(",");
        
        if(timers.preloadContent) {
            clearTimeout(timers.preloadContent);
        }
        timers.preloadContent = setTimeout(function() {
                timers.preloadContent = null;
                if(_mediaData.type == MEDIA_TYPE.TORRENT_URL) {
                    content.playlist.start(_mediaData.id, indexes, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
                }
                else if(_mediaData.type == MEDIA_TYPE.TORRENT_RAW) {
                    content.playlist.start_raw(_mediaData.id, indexes, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
                }
                else if(_mediaData.type == MEDIA_TYPE.DIRECT_URL) {
                    content.playlist.start_url(_mediaData.id, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
                }
                else if(_mediaData.type == MEDIA_TYPE.INFOHASH) {
                    content.playlist.start_infohash(_mediaData.id, indexes, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
                }
                else if(_mediaData.type == MEDIA_TYPE.PLAYER_ID) {
                    if(pluginData.version < VER_1_0_5) {
                        content.playlist.start('http://storage.torrentstream.net/get/' + _mediaData.id, indexes);
                    }
                    else {
                        content.playlist.start_player(_mediaData.id, indexes, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
                    }
                }
                else {
                }
                _log(">>>>> start preloading");
        }, 500);
        
        return true;
    }
    
    function _play(index, playConf, oncompleted)
    {
        if(!_mediaData) {
            //onMessage("alert", "mediaNotLoaded");
            // start playing when media is loaded
            _log("_play: no media data, force autoplay");
            _forceAutoplay = true;
            return;
        }
        
        var currentItem = self.playlistCurrentItem(),
            playlistSize = self.playlistSize();
        
        var defaultPlayConf = {
            position: 0,
            reset: true,
            forcePlay: false
        };
        playConf = TorrentStream.Utils.extend(defaultPlayConf, playConf);
        
        _log("_play: index=" + index + " current=" + currentItem + " playlistSize=" + playlistSize + " force=" + playConf.forcePlay + " pos=" + playConf.position + " reset=" + playConf.reset);
        if(index === undefined) {
            index = currentItem;
        }
        else {
            try {
                index = parseInt(index);
                if(isNaN(index)) {
                    index = currentItem;
                }
            }
            catch(e) {
                index = currentItem;
            }
        }
        
        if(index < 0) {
            index = 0;
        }
        else if(index >= playlistSize) {
            index = playlistSize - 1;
        }
        
        if(status.mediaState == MEDIA_STATE.IDLE || status.mediaState == MEDIA_STATE.ERROR) {
            currentItem = -1;
            _log("_play: set playingIndex to -1, media state is idle");
        }
        
        if(index == currentItem && !playConf.forcePlay) {
            // play/stop current item
            if(status.mediaState == MEDIA_STATE.STOPPED) {
                if(!conf.useInternalPlaylist) {
                    status.mediaState = MEDIA_STATE.PREBUFFERING;
                    onPrebuffering(index);
                    triggers.skipMediaState = false;
                }
                
                if(pluginData.qt) {
                    content.playlistPlay();
                }
                else {
                    content.playlist.play();
                }
            }
            else if(status.mediaState == MEDIA_STATE.PLAYING || status.mediaState == MEDIA_STATE.PAUSED) {
                if(pluginData.qt) {
                    content.playlistTogglePause();
                }
                else {
                    content.playlist.togglePause();
                }
            }
            else {
                // do nothing
                _log("_play: do nothing, mediaState=" + status.mediaState);
                onMessage("alert", "cannotPauseOnBuffering");
            }
        }
        else {
            // switch to new item
            if(!conf.useInternalPlaylist) {
                if(!oncompleted || pluginData.version == VER_1_0_3) {
                    _log("_play: stop current playing item");
                    stopContent();
                }
                _playlist.currentItem = index;
            }
            
            _log("_play: torrent: switch to item " + index);
            
            if(conf.useInternalPlaylist) {
                _log("_play:playItem: index=" + index + " pos=" + playConf.position + " stopCurrent=" + playConf.reset);
                var pos;
                if(pluginData.qt) {
                    pos = playConf.position;
                    content.playlistPlayItem(index, pos, playConf.reset);
                }
                else {
                    pos = playConf.position;
                    if(pos == 200) {
                        pos = -1;
                    }
                    content.playlist.playItem(index, pos, playConf.reset);
                }
            }
            else {
                preloadContent(index);
                waitPrebuffering();
            }
        }
    };
    
    function stopContent(fullstop)
    {
        fullstop = !!fullstop;
        _log("stopContent: fullstop=" + fullstop);
        
        if(timers.loadData) {
            _log("stopContent: stop loadData timer");
            clearTimeout(timers.loadData);
            timers.loadData = null;
        }
        
        if(conf.useInternalPlaylist) {
            if(pluginData.qt) {
                content.playlistStop(fullstop);
            }
            else {
                content.playlist.stop(fullstop);
            }
        }
        else {
            triggers.skipMediaState = true;
            if(timers.waitPrebuffering) {
                _log("stopContent: stop waitPrebuffering timer");
                clearTimeout(timers.waitPrebuffering);
                timers.waitPrebuffering = null;
            }
            
            triggers.stopClicked = true;
            
            if(fullstop) {
                content.playlist.stop(true);
                content.playlist.clear();
                triggers.lockBgProcessState = true;
                bgProcessData.state = BGP_STATE.IDLE;
            }
            else {
                if(content.playlist.items.count) {
                    content.playlist.stop();
                }
            }
            
            onStop(fullstop, self.playlistCurrentItem());
        }
    }
    
    function _pause(state)
    {
        try {
            var inputState;
            if(pluginData.qt) {
                inputState = content.inputState;
            }
            else {
                inputState = content.input.state;
            }
            
            if(state === undefined) {
                // toogle pause
                _pluginTogglePause();
            }
            else if(state === true && inputState == PLUGIN_STATE.PLAYING) {
                // pause
                _pluginTogglePause();
            }
            else if(state === false && inputState == PLUGIN_STATE.PAUSED) {
                // unpause
                _pluginTogglePause();
            }
        }
        catch(e) {
            _log("_pause: exc: " + e);
        }
    }
    
    function contentToggleMute()
    {
        try {
            if(pluginData.qt) {
                content.audioToggleMute();
            }
            else {
                if(content.audio.mute) {
                    content.audio.mute = false;
                    onMute(false);
                }
                else {
                    content.audio.mute = true;
                    onMute(true);
                }
            }
        }
        catch(e) {
            if(pluginData.muted) {
                pluginData.muted = false;
                onMute(false);
            }
            else {
                pluginData.muted = true;
                onMute(true);
            }
        }
    }
    
    function contentToggleFullscreen()
    {
        try {
            if(pluginData.qt) {
                content.videoToggleFullscreen();
            }
            else {
                content.video.fullscreen = true;
            }
        }
        catch(e) {}
    }
    
    function onMediaLoaded(mediaData)
    {
        _mediaData = mediaData;
        _log("onMediaLoaded: autoplay=" + _mediaData.autoplay + " force=" + _forceAutoplay);
        
        if(!_playerBlocked) {
            if(_mediaData.autoplay || _forceAutoplay) {
                _forceAutoplay = false;
                if(self.playlistSize() == 1) {
                    _play();
                }
            }
        }
        
        onEvent("onMediaLoaded");
    }
    
    function destroy()
    {
        _log("destroy");
        try {
            stopTimers();
            content.parentNode.removeChild(content);
            content = null;
        }
        catch(e) {
            _log("destroy: exc: " + e);
        }
    }
    
    function stopTimers()
    {
        if(timers.updateState) {
            clearTimeout(timers.updateState);
            timers.updateState = null;
        }
        if(timers.waitPrebuffering) {
            clearTimeout(timers.waitPrebuffering);
            timers.waitPrebuffering = null;
        }
        if(timers.iframeChild) {
            clearInterval(timers.iframeChild);
            timers.iframeChild = null;
        }
        if(timers.loadData) {
            clearTimeout(timers.loadData);
            timers.loadData = null;
        }
        if(timers.preloadContent) {
            clearTimeout(timers.preloadContent);
            timers.preloadContent = null;
        }
    }
    
    function onEventHandlerRegistered(handler)
    {
        if(pluginData.version < VER_1_0_5) {
            // show notification about 1.0.5
            if(typeof handler.onSystemMessage === 'function') {
                handler.onSystemMessage.call(self, "notify_version_1_0_5");
            }
        }
    }
    
    // plugin helpers
    function _pluginTogglePause() {
        if(pluginData.qt) {
            content.playlistTogglePause();
        }
        else {
            content.playlist.togglePause();
        }
    }
    
    function _pluginGetAudioVolume() {
        if(pluginData.qt) {
            return content.audioVolume;
        }
        else {
            return content.audio.volume;
        }
    }
    
    function _pluginSetAudioVolume(value) {
        if(pluginData.qt) {
            content.audioVolume = value;
        }
        else {
            content.audio.volume = value;
        }
    }
    
    function _pluginIsAd() {
        try {
            if(pluginData.qt) {
                return content.inputIsAd;
            }
            else {
                return content.input.isAd;
            }
        }
        catch(e) {
            _log("_pluginIsAd:exc: " + e);
            return false;
        }
    }
    
    function _pluginIsInterruptableAd() {
        try {
            if(pluginData.qt) {
                return content.inputIsInterruptableAd;
            }
            else {
                return content.input.isInterruptableAd;
            }
        }
        catch(e) {
            _log("_pluginIsInterruptableAd:exc: " + e);
            return false;
        }
    }
    
    function _pluginAudioTrackCount() {
        if(pluginData.qt) {
            return content.audioCount;
        }
        else {
            
        }
    }
    
    function _pluginGetAudioTrack() {
        if(pluginData.qt) {
            return content.audioTrack;
        }
        else {
            return content.audio.track;
        }
    }
    
    function _pluginSetAudioTrack(value) {
        if(pluginData.qt) {
            content.audioTrack = value;
        }
        else {
            content.audio.track = value;
        }
    }
    
    // audio channel
    function _pluginGetAudioChannel() {
        if(pluginData.qt) {
            return content.audioChannel;
        }
        else {
            return content.audio.channel;
        }
    }
    
    function _pluginSetAudioChannel(value) {
        if(pluginData.qt) {
            content.audioChannel = value;
        }
        else {
            content.audio.channel = value;
        }
    }
    
    // subtitle
    function _pluginSubtitleCount() {
        if(pluginData.qt) {
            return content.subtitleCount;
        }
        else {
            
        }
    }
    
    function _pluginGetSubtitle() {
        if(pluginData.qt) {
            return content.subtitleTrack;
        }
        else {
            return content.video.subtitle;
        }
    }
    
    function _pluginSetSubtitle(value) {
        if(pluginData.qt) {
            content.subtitleTrack = value;
        }
        else {
            content.video.subtitle = value;
        }
    }
    
    // aspect ratio
    function _pluginGetAspectRatio() {
        if(pluginData.qt) {
            return content.videoAspectRatio;
        }
        else {
            return content.video.aspectRatio;
        }
    }
    
    function _pluginSetAspectRatio(value) {
        if(pluginData.qt) {
            content.videoAspectRatio = value;
        }
        else {
            content.video.aspectRatio = value;
        }
    }
    
    // crop
    function _pluginGetCrop() {
        if(pluginData.qt) {
            return content.videoCrop;
        }
        else {
            return content.video.crop;
        }
    }
    
    function _pluginSetCrop(value) {
        if(pluginData.qt) {
            content.videoCrop = value;
        }
        else {
            content.video.crop = value;
        }
    }
    
    // public methods
    this.getEngineVersion = function() {
        try {
            return content.engineVersion;
        }
        catch(e) {
            return null;
        }
    };
    
    this.getPlugin = function() {
        return content;
    };
    
    this.getVideoAspectRatioList = function() {
        return status.video.aspect_ratio.values;
    };
    
    this.getVideoCropList = function() {
        return status.video.crop.values;
    };
    
    this.getVideoSubtitleList = function() {
        return status.video.subtitle.values;
    };
    
    this.getAudioChannelList = function() {
        return status.audio.channel.values;
    };
    
    this.getAudioTrackList = function() {
        return status.audio.track.values;
    };
    
    this.getVideoAspectRatio = function() {
        return status.video.aspect_ratio.current;
    };
    
    this.getVideoCrop = function() {
        return status.video.crop.current;
    };
    
    this.getVideoSubtitle = function() {
        return status.video.subtitle.current;
    };
    
    this.getAudioChannel = function() {
        return status.audio.channel.current;
    };
    
    this.getAudioTrack = function() {
        return status.audio.track.current;
    };
    
    this.get_player_id = function() {
        if(_mediaData.type == MEDIA_TYPE.PLAYER_ID) {
            return _mediaData.id;
        }
        else {
            return "";
        }
    };
    
    this.version = function() {
        return pluginData.stringVersion;
    };
    
    this.setVar = function(name, value) {
        vars[name] = value;
    };
    
    this.setVars = function(newVars) {
        for(name in newVars) {
            vars[name] = newVars[name];
        }
    };
    
    this.getVar = function(name, defaultValue) {
        if(defaultValue !== undefined && vars[name] === undefined) {
            return defaultValue;
        }
        return vars[name];
    };
    
    this.getVars = function() {
        return vars;
    };
    
    this.blocked = function(value) {
        if(value !== undefined) {
            _playerBlocked = !!value;
        }
        return _playerBlocked;
    };
    
    this.handleIframeMessage = function(msg) {
        gotIframeMessage(msg);
    };
    
    this.destroy = function() {
        stopContent(true);
        destroy();
    };
    
    this.play = function(index, playConf) {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        _play(index, playConf);
    };
    
    this.pause = function(state) {
        _pause(state);
    };
    
    this.next = function() {
        
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        
        var nextIndex = self.playlistCurrentItem() + 1;
        if(nextIndex >= self.playlistSize()) {
            onMessage("alert", "noNextItem");
            return;
        }
        
        if(conf.useInternalPlaylist) {
            if(pluginData.qt) {
                content.playlistNext();
            }
            else {
                content.playlist.next();
            }
        }
        else {
            triggers.nextClicked = true;
            _play(nextIndex);
        }
    };
    
    this.prev = function() {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        
        var prevIndex = self.playlistCurrentItem() - 1;
        if(prevIndex < 0) {
            onMessage("alert", "noPrevItem");
            return;
        }
        
        if(conf.useInternalPlaylist) {
            if(pluginData.qt) {
                content.playlistPrev();
            }
            else {
                content.playlist.prev();
            }
        }
        else {
            triggers.prevClicked = true;
            _play(prevIndex);
        }
    };
    
    this.stop = function(fullstop) {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        stopContent(fullstop);
    };
    
    this.is_playing = function() {
        if(pluginData.qt) {
            return content.playlistIsPlaying;
        }
        else {
            return content.playlist.isPlaying;
        }
    };
    
    this.volume = function(addVolume) {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        
        if(content) {
            var newVolume = 0, maxVolume;
            
            if(pluginData.version < VER_2_0_10) {
                addVolume = addVolume * 2;
                maxVolume = 200;
            }
            else {
                maxVolume = 100;
            }
            
            try {
                newVolume = _pluginGetAudioVolume() + addVolume;
            }
            catch(e) {
                return;
            }
            if(newVolume < 0) {
                newVolume = 0;
            }
            else if(newVolume > maxVolume) {
                newVolume = maxVolume;
            }
            _pluginSetAudioVolume(newVolume);
            
            if(!pluginData.qt) {
                if(pluginData.version < VER_2_0_10) {
                    newVolume = Math.round(newVolume / 2);
                }
                onVolume(newVolume);
                pluginData.volume = newVolume;
            }
        }
    };
	
	this.setVolume = function(newVolume) {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        
        if(content) {
            var maxVolume, volumeAdjust;
            
            if(pluginData.version < VER_2_0_10) {
                maxVolume = 200;
                volumeAdjust = 2;
            }
            else {
                maxVolume = 100;
                volumeAdjust = 1;
            }
            
		    newVolume = newVolume * volumeAdjust;
			if(newVolume < 0) {
			    newVolume = 0;
			}
			else if(newVolume > maxVolume) {
			    newVolume = maxVolume;
			}
			
			_pluginSetAudioVolume(newVolume);
			
			if(!pluginData.qt) {
                newVolume = Math.round(newVolume / volumeAdjust);
                onVolume(newVolume);
                pluginData.volume = newVolume;
            }
        }
    };
    
    this.toggleMute = function() {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        contentToggleMute();
    };
    
    this.toggleFullscreen = function() {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        contentToggleFullscreen();
    };
    
    this.position = function(pos) {
        if(_playerBlocked) {
            _log("blocked");
            return;
        }
        
        if(pos === undefined) {
            return pluginData.progress;
        }
        
        _log("position: pos=" + pos + " ad=" + _pluginIsAd());
        
            if( ! triggers.mediaStarted) {
                _log("Cannot scroll, media not started");
                return false;
            }
            
            if(_pluginIsAd() && !_pluginIsInterruptableAd()) {
                _log("position: cannot scroll ad");
                return false;
            }
            
            if(ts_status() != BGP_STATE.DOWNLOADING) {
                _log("Cannot scroll, p2p is not in 'ready' status");
                return false;
            }
            
            try {
                pos = parseFloat(pos);
                if(pluginData.qt) {
                    content.inputPosition = pos;
                }
                else {
                    content.input.position = pos;
                    pluginData.progress = pos;
                    onProgress(pos);
                }
                return true;
            }
            catch(e) {
                _log("position: exc: " + e);
            }
    };
    
    this.liveSeek = function(pos) {
        try {
            _log("liveSeek: pos=" + pos);
            content.playlistGetPlayerId("liveseek", pos, 0, 0);
        }
        catch(e) {
            _log("liveSeek:exc: " + e);
        }
    };
    
    this.getFiles = function() {
        throw "getFiles() is deprecated";
    };
    
    this.playlistToggleEnabled = function(pos) {
        var enabled;
        
        if(pluginData.qt) {
            content.playlistToggleItemState(pos);
            enabled = content.playlistItemState(pos);
        }
        else if(conf.useInternalPlaylist) {
            content.playlist.ts_toggle_item_state(pos);
            enabled = content.playlist.ts_get_item_state(pos);
        }
        else {
            enabled = _playlist.items[pos].enabled;
            enabled = !enabled;
            _playlist.items[pos].enabled = enabled;
        }
        
        return enabled;
    };
    
    this.playlistEnabled = function(pos, enabled) {
        if(enabled === undefined) {
            if(pluginData.qt) {
                return content.playlistItemState(pos);
            }
            else if(conf.useInternalPlaylist) {
                return content.playlist.ts_get_item_state(pos);
            }
            else {
                return _playlist.items[pos].enabled;
            }
        }
        
        enabled = !!enabled;
        if(pluginData.qt) {
            content.playlistSetItemState(pos, enabled);
        }
        else if(conf.useInternalPlaylist) {
            content.playlist.ts_set_item_state(pos, enabled);
        }
        else {
            _playlist.items[pos].enabled = enabled;
        }
        
        return enabled;
    };
    
    this.playlistMoveItem = function(pos, newPos) {
        if(pluginData.qt) {
            content.playlistMoveItem(pos, newPos);
        }
        else if(conf.useInternalPlaylist) {
            content.playlist.ts_move_item(pos, newPos);
        }
        else {
            var item = _playlist.items.splice(pos, 1);
            _playlist.items.splice(newPos, 0, item[0]);
        }
    };
    
    this.playlistCurrentItem = function() {
        if(pluginData.qt) {
            return content.playlistCurrentItem;
        }
        else if(conf.useInternalPlaylist) {
            return content.playlist.ts_active_item;
        }
        else {
            return _playlist.currentItem;
        }
    };
    
    this.getPlaylistItem = function(pos) {
        if(pos !== null && pos !== undefined) {
            // get item at pos
            if(pluginData.qt) {
                return {
                    name: content.playlistItemTitle(pos),
                    enabled: content.playlistItemState(pos)
                };
            }
            else if(conf.useInternalPlaylist) {
                return {
                    name: content.playlist.ts_get_item_title(pos),
                    enabled: content.playlist.ts_get_item_state(pos)
                };
            }
            else {
                
                if(!_playlist.items[pos]) {
                    throw "Invalid playlist item: " + pos;
                }
                
                var index = _playlist.items[pos].index;
                return {
                    name: _playlist.data[index],
                    enabled: _playlist.items[pos].enabled
                };
            }
        }
        else {
            // get all playlist
            var i, list = [];
            if(pluginData.qt) {
                var itemCount = content.playlistCount;
                for(i = 0; i < itemCount; i++) {
                    list.push({
                            name: content.playlistItemTitle(i),
                            enabled: content.playlistItemState(i)
                    });
                }
            }
            else if(conf.useInternalPlaylist) {
                var itemCount = content.playlist.ts_item_count;
                for(i = 0; i < itemCount; i++) {
                    list.push({
                            name: content.playlist.ts_get_item_title(i),
                            enabled: content.playlist.ts_get_item_state(i)
                    });
                }
            }
            else {
                var index;
                for(i = 0; i < self.playlistSize(); i++) {
                    index = _playlist.items[i].index;
                    list.push({
                            name: _playlist.data[index],
                            enabled: _playlist.items[i].enabled
                    });
                }
            }
            return list;
        }
    };
    
    this.playlistSize = function() {
        if(pluginData.qt) {
            return content.playlistCount;
        }
        else if(conf.useInternalPlaylist) {
            return content.playlist.ts_item_count;
        }
        else {
            return _playlist.items.length;
        }
    };
    
    this.playlistClear = function() {
        if(pluginData.qt) {
            content.playlistStop(true);
            content.playlistClear();
        }
        else {
            content.playlist.clear();
        }
    };
    
    this.audioTrack = function(val) {
        try {
            if(pluginData.version >= VER_1_0_4 && status.audio.track.count == 0) {
                return -1;
            }
            
            if(val !== undefined) {
                var currentValue = status.audio.track.current,
                    newValue = 0;
                if(val === 'next') {
                    newValue = currentValue + 1;
                }
                else if(val === 'prev') {
                    newValue = currentValue - 1;
                }
                else {
                    try {
                        newValue = parseInt(val);
                    }
                    catch(e) {
                        newValue = 0;
                    }
                }
                
                if(status.audio.track.count > 0) {
                    if(newValue < 0) {
                        newValue = status.audio.track.values.length - 1;
                    }
                    else if(newValue >= status.audio.track.values.length) {
                        newValue = 0;
                    }
                }
                
                _pluginSetAudioTrack(newValue);
                status.audio.track.current = _pluginGetAudioTrack();
            }
            return status.audio.track.current;
        }
        catch(e) {
            _log("audioTrack: " + e);
            return -1;
        }
    };
    
    this.audioChannel = function(val) {
        try {
            if(val !== undefined) {
                var currentValue = status.audio.channel.current,
                    newValue = 0;
                if(val === 'next') {
                    newValue = currentValue + 1;
                }
                else if(val === 'prev') {
                    newValue = currentValue - 1;
                }
                else {
                    try {
                        newValue = parseInt(val);
                    }
                    catch(e) {
                        newValue = 0;
                    }
                }
                
                if(newValue < 0) {
                    newValue = status.audio.channel.values.length - 1;
                }
                else if(newValue >= status.audio.channel.values.length) {
                    newValue = 0;
                }
                
                _pluginSetAudioChannel(newValue);
                status.audio.channel.current = _pluginGetAudioChannel();
            }
            return status.audio.channel.current;
        }
        catch(e) {
            _log("audioChannel: " + e);
            return -1;
        }
    };
    
    this.subtitle = function(val) {
        if(pluginData.version >= VER_1_0_4 && status.video.subtitle.count == 0) {
            return -1;
        }
        
        try {
            if(val !== undefined) {
                var currentValue = status.video.subtitle.current,
                newValue = 0;
                if(val === 'next') {
                    newValue = currentValue + 1;
                }
                else if(val === 'prev') {
                    newValue = currentValue - 1;
                }
                else {
                    try {
                        newValue = parseInt(val);
                    }
                    catch(e) {
                        newValue = 0;
                    }
                }
                
                _log("subtitle: newValue=" + newValue);
                _log("subtitle: count=" + status.video.subtitle.count);
                
                if(status.video.subtitle.count > 0) {
                    if(newValue < 0) {
                        newValue = status.video.subtitle.values.length - 1;
                    }
                    else if(newValue >= status.video.subtitle.values.length) {
                        newValue = 0;
                    }
                }
                
                _log("subtitle: newValue=" + newValue);
                _pluginSetSubtitle(newValue);
                status.video.subtitle.current = _pluginGetSubtitle();
                _log("subtitle: current=" + status.video.subtitle.current);
            }
            return status.video.subtitle.current;
        }
        catch(e) {
            _log("subtitle(" + val + "): " + e);
            return -1;
        }
    };
    
    this.aspectRatio = function(val) {
        try {
            if(val !== undefined) {
                var currentValue = status.video.aspect_ratio.current,
                newValue = 0;
                if(val === 'next') {
                    newValue = currentValue + 1;
                }
                else if(val === 'prev') {
                    newValue = currentValue - 1;
                }
                else {
                    try {
                        newValue = parseInt(val);
                    }
                    catch(e) {
                        newValue = 0;
                    }
                }
                
                if(newValue < 0) {
                    newValue = status.video.aspect_ratio.values.length - 1;
                }
                else if(newValue >= status.video.aspect_ratio.values.length) {
                    newValue = 0;
                }
                
                if(newValue == 0) {
                    _pluginSetAspectRatio("");
                }
                else {
                    _pluginSetAspectRatio(status.video.aspect_ratio.values[newValue]);
                }
                status.video.aspect_ratio.current = newValue;
            }
            return status.video.aspect_ratio.current;
        }
        catch(e) {
            _log("aspectRatio: " + e);
            return 0;
        }
    };
    
    this.crop = function(val) {
        if(pluginData.version == VER_1_0_2) {
            onSystemMessage("old_version_no_crop");
            return;
        }
        
        try {
            if(val !== undefined) {
                var currentValue = status.video.crop.current,
                newValue = 0;
                if(val === 'next') {
                    newValue = currentValue + 1;
                }
                else if(val === 'prev') {
                    newValue = currentValue - 1;
                }
                else {
                    try {
                        newValue = parseInt(val);
                    }
                    catch(e) {
                        newValue = 0;
                    }
                }
                
                if(newValue < 0) {
                    newValue = status.video.crop.values.length - 1;
                }
                else if(newValue >= status.video.crop.values.length) {
                    newValue = 0;
                }
                
                if(newValue == 0) {
                    _pluginSetCrop("");
                }
                else {
                    _pluginSetCrop(status.video.crop.values[newValue]);
                }
                status.video.crop.current = newValue;
            }
            return status.video.crop.current;
        }
        catch(e) {
            _log("crop: " + e);
            return 0;
        }
    };
    
    this.getAffiliateId = function() {
        return _mediaData ? _mediaData.affiliateId : 0;
    };
    
    this.registerEventHandler = function(handler) {
        if(typeof handler !== 'object') {
            throw "Event handler must be an object";
        }
        
        onEventHandlerRegistered(handler);
        eventHandlers.push(handler);
    };
    
    this.loadTorrent = function(url, conf) {
        if(typeof conf !== 'object') {
            conf = {};
        }
        
        url = url.replace(new RegExp('\\s+', 'g'), '%20');
        
        conf.type = MEDIA_TYPE.TORRENT_URL;
        conf.id = url;
        loadPlaylist(conf);
    };
    
    this.loadRawTorrent = function(url, conf) {
        if(typeof conf !== 'object') {
            conf = {};
        }
        conf.type = MEDIA_TYPE.TORRENT_RAW;
        conf.id = url;
        loadPlaylist(conf);
    };
    
    this.loadUrl = function(url, conf) {
        if(typeof conf !== 'object') {
            conf = {};
        }
        conf.type = MEDIA_TYPE.DIRECT_URL;
        conf.id = url;
        loadPlaylist(conf);
    };
    
    this.loadInfohash = function(infohash, conf) {
        if(typeof conf !== 'object') {
            conf = {};
        }
        conf.type = MEDIA_TYPE.INFOHASH;
        conf.id = infohash;
        loadPlaylist(conf);
    };
    
    this.loadPlayer = function(playerId, conf) {
        if(typeof conf !== 'object') {
            conf = {};
        }
        conf.type = MEDIA_TYPE.PLAYER_ID;
        conf.id = playerId;
        loadPlaylist(conf);
    };
    
    this.getAuthLevel = function() {
        return _authLevel();
    };
    
    this.state = function() {
        return status.mediaState;
    };
    
    this.getPlayerId = function() {
        if( ! _mediaData) {
            return null;
        }
        
        if(_mediaData.type == MEDIA_TYPE.PLAYER_ID) {
            return _mediaData.id;
        }
        
        if( ! _mediaData.infohash) {
            return null;
        }
        
        try {
            _log("getPlayerId: infohash=" + _mediaData.infohash + " d=" + _mediaData.developerId + " a=" + _mediaData.affiliateId + " z=" + _mediaData.zoneId);
            
            var playerId;
            if(pluginData.qt) {
                playerId = content.playlistGetPlayerId(_mediaData.infohash, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
            }
            else {
                playerId = content.playlist.getPlayerId(_mediaData.infohash, _mediaData.developerId, _mediaData.affiliateId, _mediaData.zoneId);
            }
            
            _log("getPlayerId: playerId=" + playerId);
            return playerId;
        }
        catch(e) {
            _log("getPlayerId:exc: " + e);
            return "";
        }
    };
    
    ////////////////////////////////////////////////////////////////////////////
    // init
    _log("constructor: container=" + container);
    
    var defaultConf = {
        useInternalPlaylist: true,
        useInternalControls: false,
        liveStreamControls: false,
        embedStyle: null,
        firefoxUnwrapEmbedObjects: false,
        embedWaitTime: 0,
        debug: false,
        bgColor: "000000",
        fontColor: "ffffff",
        sendOnCompleted: false, // send emulated onCompleted event (when we got onStop and the position is almost at the end)
        needAuthToSeek: false   // user need to be authorized to seek
    };
    conf = TorrentStream.Utils.extend(defaultConf, conf);
    
    if(conf.vars) {
        vars = conf.vars;
    }
    
    if(typeof conf.eventHandler === 'object') {
        self.registerEventHandler(conf.eventHandler);
    }
    
    // check whether plugin installed
    var availablePlugin = TorrentStream.Utils.detectPluginExt();
    if(availablePlugin.type == 0) {
        throw "plugin_not_installed";
    }
    if(!availablePlugin.enabled) {
        throw "plugin_not_enabled";
    }
    
    var useQtPlugin;
    _platform = TorrentStream.Utils.detectPlatform();
    _browser = TorrentStream.Utils.detectBrowser();
    if(_platform == 'windows') {
        if(availablePlugin.type == 2 || availablePlugin.type == 3) {
            useQtPlugin = true;
        }
        else {
            useQtPlugin = false;
        }
    }
    else {
        useQtPlugin = true;
    }
    _log("constructor: platform=" + _platform + " browser=" + _browser + " availablePlugin=" + availablePlugin.type + " useQtPlugin=" + useQtPlugin);

    pluginData.type = availablePlugin.type;
    if(useQtPlugin) {
        pluginData.qt = true;
    }
    else {
        pluginData.qt = false;
    }
    
    var asyncEmbed = (typeof conf.embedCallback === "function");
    
    function afterEmbed(embedObject) {
        // check whether embed was successfull
        if(!embedObject) {
            if(asyncEmbed) {
                conf.embedCallback.call(self, false, "plugin_not_enabled");
                return;
            }
            else {
                throw "plugin_not_enabled";
            }
        }
        
        content = embedObject;
        
        // check plugin version
        if(content.version) {
            pluginData.stringVersion = content.version;
        }
        pluginData.version = getVersion(pluginData.stringVersion);
        _log("version=" + pluginData.stringVersion + " auth=" + _authLevel());
        
        if(pluginData.version < VER_1_0_5) {
            conf.useInternalPlaylist = false;
            _playlist = {
                data: {},
                items: [],
                currentItem: -1
            };
            //throw "force_version_1_0_5";
        }
        
        // check version compatibility
        if(pluginData.version == VER_1_0_2) {
            if(asyncEmbed) {
                conf.embedCallback.call(self, false, "old_version_1_0_2");
                return;
            }
            else {
                throw "old_version_1_0_2";
            }
        }
        
        if(pluginData.events) {
            _log("afterEmbed: attach events");
            attachPluginEvents();
            
            if(status.mediaState == MEDIA_STATE.CONNECTING) {
                if(content.state != -1 && content.state != BGP_STATE.CONNECTING) {
                    _log("afterEmbed: bg connected");
                    status.mediaState = MEDIA_STATE.IDLE;
                    onConnected();
                }
            }
        }
        else {
            if(conf.useInternalPlaylist) {
                triggers.lockBgProcessState = false;
                triggers.skipMediaState = false;
            }
            
            // init state timer
            if(pluginData.version < VER_1_0_5) {
                // skip "connecting" state for old versions
                status.mediaState = MEDIA_STATE.IDLE;
                onConnected();
            }
            updateState();
        }
        
        if(asyncEmbed) {
            conf.embedCallback.call(self, true); 
        }
    }
    
    // Load plugin
    if(asyncEmbed) {
        embedPlugin(container, conf.useInternalPlaylist, conf.embedStyle, conf.bgColor, conf.fontColor, afterEmbed);
    }
    else {
        content = embedPlugin(container, conf.useInternalPlaylist, conf.embedStyle, conf.bgColor, conf.fontColor);
        afterEmbed(content);
    }
}

