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

    var original = document.title;
    var timeout;

    window.flashTitle = function (newMsg, howManyTimes) {
        function step() {
            document.title = (document.title == original) ? '[' + newMsg + '] ' + original : original;

            if (--howManyTimes > 0) {
                timeout = setTimeout(step, 1000);
            }
        }

        howManyTimes = parseInt(howManyTimes);

        if (isNaN(howManyTimes)) {
            howManyTimes = 5;
        }

        cancelFlashTitle(timeout);
        step();
    }

    window.cancelFlashTitle = function () {
        clearTimeout(timeout);
        document.title = original;
    }

    // connect to socket
    if (typeof io !== 'undefined' && typeof eventsConfig != 'undefined') {
        var socketUrl = eventsConfig.host + ':' + eventsConfig.port;
        var socket = io.connect(socketUrl);

        socket.on('connect', function () {
            socket.emit('join', eventsConfig);
        });


        socket.on('newEvent', function (msg) {
            flashTitle(eventsConfig.msg, 10);

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
        });

        window.onbeforeunload = function () {
            socket.disconnect();
        }
    }

    $(document).on('click', '#markAllEventsAsRead', function(e) {
        e.preventDefault();
        var elem = $(this);

        $.ajax({
            url: elem.attr('href'),
            type: 'post',
            dataType: 'json',
            success: function (data) {
                $('#eventsList').remove();
                eventsMenu.find('.badge').remove();
                eventsMenu.find('.caret').remove();
            }
        });
    });
});