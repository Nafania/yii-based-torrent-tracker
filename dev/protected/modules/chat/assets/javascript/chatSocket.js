$(function () {
    if (typeof io == 'undefined' && typeof mjmChatConfig != 'undefined') {
        return false;
    }

    var user = mjmChatConfig.user;
    var mjmChatHostPort = mjmChatConfig.host + ':' + mjmChatConfig.port;
    var socket = io.connect(mjmChatHostPort);

    socket.on('connect', function () {

        socket.on('newMessage', function (data) {
            if ( !data.user ) {
                return false;
            }
            var time = new moment(data.time), maxMergeTime = 60;
            var hl = '';
            var pos = data.message.indexOf(user.name, 0); // returns -1
            if (pos >= 0) {
                hl = ' chatHighlight';
            }

            var $lastDiv = $("#chatMessages").find('.media:last');
            if (typeof $lastDiv != 'undefined' && $lastDiv.data('uid') == data.user.id && ( time.format('X') - new moment($lastDiv.find('abbr').data('isotimestamp')).format('X') < maxMergeTime ) ) {
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

        socket.on('clear', function() {
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
    if ($.cookie('showChat') == 1) {
        socket.emit('addUser', user);
    }

    $("#mjmChatRoomMinimize, #mjmChatRoomTitle").click(function () {
        if ($("#mjmChatRoom").css('bottom') == '0px') {
            socket.emit('closeChat');
            $.cookie("showChat", 0, {path: '/'});
            $("#mjmChatRoom").animate({bottom: "-400px", right: '-500px'}, 'fast');
        }
        else {
            socket.emit('addUser', user);
            $.cookie("showChat", 1, {path: '/'});
            $("#mjmChatRoom").animate({bottom: "0", right: '0'}, 'fast');
        }
    });
    // sent message to socket
    $("#mjmChatSend").click(function () {
        if ($('#mjmChatMessage').val() != '') {
            socket.emit('newMessage', $('#mjmChatMessage').val());
            $('#mjmChatMessage').val('');
        }
        $('#mjmChatMessage').focus();
    });

    $('#mjmChatMessage').keypress(function (e) {
        if (e.which == 13) {
            $('#mjmChatSend').focus().click();
            e.preventDefault();
        }
    });

    $(document).on('click', '[data-action=chatusername]', function (e) {
        e.preventDefault();
        var html = $('#mjmChatMessage').val();
        $('#mjmChatMessage').val(html + ( html ? ' ' : '' ) + $(this).html() + ': ');
    });

    function mjmChatScrollDown() {
        var height = $('#chatMessages')[0].scrollHeight;
        $('#chatMessages').scrollTop(height);
    }
});