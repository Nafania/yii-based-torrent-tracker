$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
    if (originalOptions.type == 'POST' || options.type == 'POST') {
        var data = originalOptions.data;
        if (originalOptions.data !== undefined) {
            if (Object.prototype.toString.call(originalOptions.data) === '[object String]') {
                data = $.deparam(originalOptions.data); // see http://benalman.com/code/projects/jquery-bbq/examples/deparam/
            }
        }
        else {
            data = {};
        }
        options.data = $.param($.extend(data, { csrf: $('meta[name="csrf"]').attr('content') }));
    }
});
$(document).ajaxError(function (event, request, settings) {
    var data = eval("(" + request.responseText + ")");
    var errTxt = '';
    if (!data) {
        return;
    }
    if (data.data.errors) {
        $.each(data.data.errors, function (key, val) {
            errTxt += val + "\n";
        });
    } else {
        errTxt = data.message;
    }
    $('.top-right').notify({
        message: { html: errTxt },
        fadeOut: {
            enabled: true,
            delay: 9000
        },
        type: 'error'
    }).show();
});

$(function () {
    $('body').append('<div class="notifications top-right"></div>');
    $('.loading').ajaxStart(function () {
        $(this).show();
    }).ajaxStop(function () {
            $(this).hide();
        });

    $(document).on('click', 'a[href="/user/login"]', function (e) {
        e.preventDefault();
        $("#loginModal").modal("show")
    });
});