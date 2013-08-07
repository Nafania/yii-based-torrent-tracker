$(document).on('click', 'a[data-action=report]', function (e) {
    e.preventDefault();

    var btn = $(this), url = btn.attr('href');
    var reportForId = $(this).data('report-for');
    if (btn.hasClass('disabled')) {
        return false;
    }

    if (url.indexOf('#') == 0) {
        $(url).modal('open')
    } else {
        $.getJSON(url, function (data) {
            if (typeof data.data == 'undefined' || typeof data.data.view == 'undefined') {
                return false;
            }

            $('#reportModal').modal('show');

            $('#reportModal').modal().on('shown',function () {
                $(this).html(data.data.view);
            }).on('hidden', function () {
                    $(this).html('');
                    btn.addClass('disabled');
                })
        })
    }
});
