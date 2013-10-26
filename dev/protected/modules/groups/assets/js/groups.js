$(document).on('click', 'a[data-action=groupInvite]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');
});
$(document).on('click', 'a[data-action=join]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');
    var resetText = '';

    $.ajax({
        url: url,
        dataType: 'json',
        type: 'POST',
        success: function (data) {
            if (url.indexOf('unJoin', 0) > 0) {
                elem.attr('href', url.replace('unJoin', 'join'));
            }
            else {
                //elem.attr('href', url.replace('join', 'unJoin'));
            }
            if (typeof data.data.newText !== 'undefined') {
                resetText = data.data.newText;
            }
            $('.top-right').notify({
                message: { html: data.message },
                fadeOut: {
                    enabled: true,
                    delay: 3000
                },
                type: 'success'
            }).show();
        },
        complete: function (data) {
            elem.button('reset');
            if (resetText) {
                elem.html(resetText);
            }
        }
    });
});

$(document).on('click', 'a[data-action=changeStatus]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');

    $.ajax({
        url: url,
        dataType: 'json',
        type: 'POST',
        success: function (data) {
            $('.top-right').notify({
                message: { html: data.message },
                fadeOut: {
                    enabled: true,
                    delay: 3000
                },
                type: 'success'
            }).show();
            var span = elem.prevAll('.statusLabel');

            if (span.length) {
                span.html(data.data.newStatus);
                elem.next('.btn').remove();
                elem.prev('.btn').remove();
                elem.remove();
            }
            else {
                elem.parents('.media').fadeOut('fast').remove();
            }
        },
        complete: function (data) {
            elem.button('reset');
        }
    });
});
$(document).on('mouseenter', '.groupsList, .groupView', function (e) {
    $(this).find('.groupOperations').fadeIn('fast');
});
$(document).on('mouseleave', '.groupsList, .groupView', function (e) {
    $(this).find('.groupOperations').fadeOut('fast');
});

$(document).on('click', 'a[data-action=groupInvite]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');

    elem.addClass('load');

    $.ajax({
        url: url,
        dataType: 'json',
        type: 'POST',
        success: function (data) {
            if (!$('#inviteModal').length) {
                $('<div class="modal hide fade" id="inviteModal"></div>').appendTo('body');
            }
            var modal = $('#inviteModal').modal();
            modal.html(data.data.view);
            modal.modal('show');

            modal.modal().on('hidden', function () {
                $(this).html('');
                elem.removeClass('load');
            });
        }
    });
});



