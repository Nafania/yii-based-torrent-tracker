var io = require('socket.io')
    , redis = require('redis')
    , sub = redis.createClient();

io = io.listen(8000);

io.configure(function () {
    io.enable('browser client minification');  // send minified client
    io.enable('browser client etag');          // apply etag caching logic based on version number
    io.enable('browser client gzip');          // gzip the file
    io.set('log level', 1);
});

sub.select(2, function () { /* ... */
});

sub.on("error", function (err) {
    console.log("Error " + err);
});

sub.on('message', function (channel, message) {
    console.log(new Date(), channel, message);
    //socket.broadcast.to(channel).emit(message);
    io.sockets.emit('newEvent', channel, message);
});

io.sockets.on('connection', function (socket) {

    socket.on('join', function (eventsConfig) {
        sub.subscribe(eventsConfig.prefix + eventsConfig.hash);
        socket.join(eventsConfig.prefix + eventsConfig.hash);
    });

    socket.on('newEvent', function (channel, message) {
        socket.broadcast.to(channel).emit(message);
    });

    socket.on('disconnect', function () {
        sub.unsubscribe();
        socket.leave();
    });
});
