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
        options.data = $.param($.extend(data, { csrf:$('meta[name="csrf"]').attr('content') }));
    }
});
$(function() {
    $('.loading').ajaxStart(function () {$(this).show();}).ajaxStop(function () {$(this).hide();});
});