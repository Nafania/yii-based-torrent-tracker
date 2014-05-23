// Imports
var io = require('socket.io')
    , redis = require('redis')
    , users = {}
    , pub = redis.createClient()
    , sub = redis.createClient()
    , store = redis.createClient();

io = io.listen(8001);

io.configure(function () {
    io.enable('browser client minification');  // send minified client
    io.enable('browser client etag');          // apply etag caching logic based on version number
    io.enable('browser client gzip');          // gzip the file
    io.set('log level', 1);
});

io.sockets.on('connection', function (client) {

    store.lrange('chat', 0, 20, function (e, messages) {
        try {
            messages.reverse();
            messages.forEach(function (message) {
                client.emit('newMessage', JSON.parse(message));
            });
        }
        catch (e) {
            console.log('err: ' + e);
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
        message = nl2br(htmlEntities(message));
        var time = Date.now();

        var obj = {
            user: users[client.userId],
            message: message,
            time: time
        }

        store.lpush('chat', JSON.stringify(obj), function (e, r) {
            io.sockets.emit('newMessage', obj);
        });
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
