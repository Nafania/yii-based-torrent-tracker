var io = require('socket.io').listen(8000);

io.sockets.on('connection', function (socket) {
    socket.on('join', function (room) {
        socket.join(room);
    });

    socket.on('newEvent', function (data) {
        socket.broadcast.to(data.room).emit('newEvent');
    })
});
