$(function () {
    var eventsMenu = $('#eventsMenu');
    eventsMenu.click(function (e) {
        e.preventDefault();
        if (eventsMenu.parents('li').hasClass('open')) {
            $('#eventsList').remove();
            return;
        }
        $.ajax({
            url: eventsMenu.attr('href'),
            type: 'post',
            dataType: 'json',
            success: function (data) {
                $(data.data.view).insertAfter(eventsMenu);
            }
        });
    });
    // connect to socket
    if (typeof io !== 'undefined' && typeof eventsConfig != 'undefined') {
        var socketUrl = eventsConfig.host + ':' + eventsConfig.port;
        var socket = io.connect(socketUrl);

        socket.on('connect', function () {
            socket.emit('join', eventsConfig.hash);
        });

        socket.on('newEvent', function (msg) {
            var span = eventsMenu.find('.badge'), num;
            if (span.length) {
                num = parseInt(span.html());
                num += 1;
                span.html(num);
            }
            else {
                //eventsMenu.parents('li').addClass('dropdown');
                eventsMenu.append('<span class="badge badge-success">1</span><span class="caret"></span>');
            }
        })
    }
});