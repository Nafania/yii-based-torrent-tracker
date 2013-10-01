$(document).on('click', 'a[data-action=report]', function (e) {
    e.preventDefault();

    var btn = $(this), url = btn.attr('href');
    var modelId = $(this).data('id');
    var modelName = $(this).data('model');
    var modal = $('#reportModal');
    if (btn.hasClass('disabled')) {
        return false;
    }

    if (url.indexOf('#') == 0) {
        $(url).modal('open')
    } else {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url,
            data: {modelName: modelName, modelId: modelId },
            cache: true,
            success: function (data) {
                if (typeof data.data == 'undefined' || typeof data.data.view == 'undefined') {
                    return false;
                }

                modal.html(data.data.view);
                modal.modal('show');

                modal.modal().on('hidden', function () {
                    $(this).html('');
                    btn.addClass('disabled');
                });
            }
        });
    }
});
