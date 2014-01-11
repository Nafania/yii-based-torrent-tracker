$(function () {
    if (typeof io == 'undefined' && typeof mjmChatConfig != 'undefined') {
        return false;
    }
    
    var socket, room = $('#mjmChatRoom'), message = $('#mjmChatMessage');

    var connectToSocket = function () {
        var user = mjmChatConfig.user;
        var mjmChatHostPort = mjmChatConfig.host + ':' + mjmChatConfig.port;
        socket = io.connect(mjmChatHostPort);

        socket.emit('addUser', user);

        socket.on('connect', function () {

            socket.on('history', function (history) {
                for (var key in history) {
                    var data = history[key];

                    if (!data.user) {
                        continue;
                    }
                    var time = new moment(data.time), maxMergeTime = 60;
                    var hl = '';
                    var pos = data.message.indexOf(user.name, 0); // returns -1
                    if (pos >= 0) {
                        hl = ' chatHighlight';
                    }

                    var $lastDiv = $("#chatMessages").find('.media:last');
                    if (typeof $lastDiv != 'undefined' && $lastDiv.data('uid') == data.user.id && ( time.format('X') - new moment($lastDiv.find('abbr').data('isotimestamp')).format('X') < maxMergeTime )) {
                        $lastDiv.find('.commentText').append('<br>' + data.message);
                    }
                    else {
                        var html = '<div class="media' + hl + '" data-uid="' + data.user.id + '">\
                    	<a href="' + data.user.url + '" class="pull-left"><img width="32" height="32" alt="' + data.user.name + '" src="' + data.user.avatar + '" class="media-object"></a>\
                    	<div class="media-body">\
                    		<div class="comment">\
                            <h6 class="media-heading">\
                    	        <a href="' + data.user.url + '" data-action="chatusername">' + data.user.name + '</a><span class="userRating ' + data.user.ratingClass + '">' + data.user.rating + '</span>, <abbr data-isotimestamp="' + time.toISOString() + '" data-livestamp="' + time.toISOString() + '" title="' + time.format('DD.MM.YY HH:mm') + '">' + time.fromNow() + '</abbr>\
                    	        </h6>\
                                 <div class="commentText">' + data.message + '</div>\
                    					</div>\
                    			</div>\
                    </div>';
                        $("#chatMessages").append(html);
                    }
                }
                mjmChatScrollDown();
            });

            socket.on('newMessage', function (data) {
                if (!data.user) {
                    return false;
                }
                var time = new moment(data.time), maxMergeTime = 60;
                var hl = '';
                var pos = data.message.indexOf(user.name, 0); // returns -1
                if (pos >= 0) {
                    hl = ' chatHighlight';
                }

                var $lastDiv = $("#chatMessages").find('.media:last');
                if (typeof $lastDiv != 'undefined' && $lastDiv.data('uid') == data.user.id && ( time.format('X') - new moment($lastDiv.find('abbr').data('isotimestamp')).format('X') < maxMergeTime )) {
                    $lastDiv.find('.commentText').append('<br>' + data.message);
                }
                else {
                    var html = '<div class="media' + hl + '" data-uid="' + data.user.id + '">\
            	<a href="' + data.user.url + '" class="pull-left"><img width="32" height="32" alt="' + data.user.name + '" src="' + data.user.avatar + '" class="media-object"></a>\
            	<div class="media-body">\
            		<div class="comment">\
                    <h6 class="media-heading">\
            	        <a href="' + data.user.url + '" data-action="chatusername">' + data.user.name + '</a><span class="userRating ' + data.user.ratingClass + '">' + data.user.rating + '</span>, <abbr data-isotimestamp="' + time.toISOString() + '" data-livestamp="' + time.toISOString() + '" title="' + time.format('DD.MM.YY HH:mm') + '">' + time.fromNow() + '</abbr>\
            	        </h6>\
                         <div class="commentText">' + data.message + '</div>\
            					</div>\
            			</div>\
            </div>';
                    $("#chatMessages").append(html);
                }
                mjmChatScrollDown();
            });

            socket.on('status', function (data) {
                $("#chatMessages").append('<li class=\'mjmChatEvent\'>' + data + '</li>');
                mjmChatScrollDown();
            });

            socket.on('clear', function () {
                $("#chatMessages").html('');
            });

            socket.on('renewUsers', function (users) {
                $('#mjmChatUsersList').empty();
                $.each(users, function (key, value) {
                    if (!value) {
                        return;
                    }
                    $("#mjmChatUsersList").append('<li><a href="' + value.url + '">' + value.name + '</a><span class="userRating ' + value.ratingClass + '">' + value.rating + '</span></li>');
                });
            });
        });
    }

    window.onbeforeunload = function () {
        socket.disconnect();
    }

    if ($.cookie('showChat') == 1) {
        connectToSocket();
    }

    $("#mjmChatRoomMinimize, #mjmChatRoomTitle").click(function () {
        if (room.hasClass('chatUp')) {
            socket.disconnect();
            $.cookie("showChat", 0, {path: '/'});
            room.removeClass('chatUp').addClass('chatDown');
        }
        else {
            connectToSocket();

            $.cookie("showChat", 1, {path: '/'});
            room.removeClass('chatDown').addClass('chatUp');
        }
    });

    // sent message to socket
    $("#mjmChatSend").click(function () {
        if (message.val() != '') {
            socket.emit('newMessage', message.val());
            message.val('');
        }
        message.focus();
    });

    message.keypress(function (e) {
        if (e.which == 13) {
            $('#mjmChatSend').focus().click();
            e.preventDefault();
        }
    });

    $(document).on('click', '[data-action=chatusername]', function (e) {
        e.preventDefault();
        var html = message.val();
        message.val(html + ( html ? ' ' : '' ) + $(this).html() + ': ');
    });

    function mjmChatScrollDown() {
        var height = $('#chatMessages')[0].scrollHeight;
        $('#chatMessages').scrollTop(height);
    }
});