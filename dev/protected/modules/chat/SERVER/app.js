// Imports
var io = require('socket.io')
    , redis = require('redis')
    , users = {}
    , history = [];

io = io.listen(8001);

var store = redis.createClient();

io.sockets.on('connection', function (client) {

    store.get('history', function (err, res) {
        history = JSON.parse(res);
        if (typeof history == 'object') {
            for (key in history) {
                io.sockets.emit('newMessage', history[key]);
            }
        }
        else {
            history = [];
        }
    });

    client.on('addUser', function (user) {
        client.name = user.name;
        client.userId = user.id;

        users[user.id] = user;

        client.broadcast.emit('status', user.name + ' has joined');

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
        store.set('history', JSON.stringify(history));

        io.sockets.emit('newMessage', obj);
    });

    client.on('disconnect', function () {
        delete users[client.userId];
        client.broadcast.emit('status', client.name + ' has left');
        client.leave();

        io.sockets.emit('renewUsers', mjmChatGetUsers(users));
    });

    client.on('closeChat', function () {
        delete users[client.userId];
        client.broadcast.emit('status', client.name + ' has left');
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
