// Imports
var io = require('socket.io')
    , redis = require('redis')
    , redisStore = require('socket.io/lib/stores/redis')
    //, heapdump = require('heapdump')
    , users = {}
    , history = []
    , maxHistoryLength = 40
    , pub = redis.createClient()
    , sub = redis.createClient()
    , client = redis.createClient();

io = io.listen(8001);

//var store = redis.createClient();

io.configure(function () {
    io.enable('browser client minification');  // send minified client
    io.enable('browser client etag');          // apply etag caching logic based on version number
    io.enable('browser client gzip');          // gzip the file
    io.set('log level', 1);                    // reduce logging
    io.set('store', new redisStore({
        redisPub: pub, redisSub: sub, redisClient: client
    }));
});
//io.set('log level', 1); // reduce logging
/*io.set('transports', [
 'websocket'
 , 'flashsocket'
 , 'htmlfile'
 , 'jsonp-polling'
 ]);*/
/*var nextMBThreshold = 0;
setInterval(function () {
    var memMB = process.memoryUsage().rss / 1048576;
    if (memMB > nextMBThreshold) {
        heapdump.writeSnapshot();
        nextMBThreshold += 100;
    }
}, 6000 * 2);*/

io.sockets.on('connection', function (client) {

    pub.get('history', function (err, res) {
        history = JSON.parse(res);
        if (typeof history == 'object') {
            var newHistory = slice(history, history.length - maxHistoryLength, history.length);
            client.emit('history', newHistory);
        }
        else {
            history = [];
        }
    });

    client.on('addUser', function (user) {
        client.name = user.name;
        client.userId = user.id;

        users[user.id] = user;

        //client.broadcast.emit('status', user.name + ' has joined');

        io.sockets.emit('renewUsers', mjmChatGetUsers(users));
    });

    client.on('newMessage', function (message) {
        var message = nl2br(htmlEntities(message));
        var time = Date.now();

        var obj = {
            user: users[client.userId],
            message: message,
            time: time
        }

        history.push(obj);
        pub.set('history', JSON.stringify(history));

        io.sockets.emit('newMessage', obj);
    });

    client.on('disconnect', function () {
        delete users[client.userId];
        //client.broadcast.emit('status', client.name + ' has left');
        client.leave();

        io.sockets.emit('renewUsers', mjmChatGetUsers(users));
    });

    client.on('closeChat', function () {
        delete users[client.userId];
        //client.broadcast.emit('status', client.name + ' has left');
        io.sockets.emit('renewUsers', mjmChatGetUsers(users));
    });
});

function mjmChatChangeUserIfExist(user) {
    var i = 1;
    do {
        i++;
        var checkExist = users.indexOf(user + i.toString());
    }
    while (checkExist != -1);

    return 'Guest' + i.toString();
}

function mjmChatGetUsers(users) {
    var _users = [];
    for (key in users) {
        _users[users[key].id] = users[key];
    }
    return _users;
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// phpjs.org/functions/nl2br
function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

/**
 * Slices the object. Note that returns a new spliced object,
 * e.g. do not modifies original object. Also note that that sliced elements
 * are sorted alphabetically by object property name.
 */
http://stackoverflow.com/questions/4401120/get-a-slice-of-a-javascript-associative-array
    function slice(obj, start, end) {

        var sliced = {};
        var i = 0;
        for (var k in obj) {
            if (i >= start && i < end)
                sliced[k] = obj[k];

            i++;
        }

        return sliced;
    }
