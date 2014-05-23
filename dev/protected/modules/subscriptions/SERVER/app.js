var io = require('socket.io')
    , redis = require('redis')
    , sub = redis.createClient();

io = io.listen(8003);

sub.select(1, function() { /* ... */ });

sub.on("error", function (err) {
    console.log("Error " + err);
});

io.configure(function () {
    io.enable('browser client minification');  // send minified client
    io.enable('browser client etag');          // apply etag caching logic based on version number
    io.enable('browser client gzip');          // gzip the file
    io.set('log level', 1);                    // reduce logging
});

io.sockets.on('connection', function (socket) {

    socket.on('join', function (eventsConfig) {
        sub.subscribe(eventsConfig.prefix + eventsConfig.hash);
        socket.join(eventsConfig.prefix + eventsConfig.hash);

        console.log(new Date(), eventsConfig.hash + ' connected');
    });

    sub.on('message', function (channel, message) {
        console.log(new Date(), channel, message);
        socket.broadcast.to(channel).emit(message);
    });

    socket.on('disconnect', function () {
        console.log(new Date(), 'disconnected');
        sub.unsubscribe();
        socket.leave();
    });
});
