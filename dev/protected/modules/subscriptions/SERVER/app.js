var io = require('socket.io');

io = io.listen(8000);
io.configure(function () {
    io.enable('browser client minification');  // send minified client
    io.enable('browser client etag');          // apply etag caching logic based on version number
    io.enable('browser client gzip');          // gzip the file
    io.set('log level', 1);                    // reduce logging
});

io.sockets.on('connection', function (socket) {
    socket.on('join', function (room) {
        socket.join(room);
    });

    socket.on('newEvent', function (data) {
        socket.broadcast.to(data.room).emit('newEvent');
    });

    socket.on('disconnect', function () {
        socket.leave();
    });
});
