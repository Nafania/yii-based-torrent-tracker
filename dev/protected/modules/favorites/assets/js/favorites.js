$(document).on('click', 'a[data-action=favorites]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');

    if (elem.hasClass('btn')) {
        elem.addClass('load');
    }
    else {
        elem.children('i').addClass('icon-load');
    }

    $.ajax({
        url: url,
        data: {modelName: elem.data('model'), modelId: elem.data('id')},
        dataType: 'json',
        type: 'POST',
        success: function (data) {
            if (elem.children('i').hasClass('icon-star-empty')) {
                elem.attr('href', url.replace('create', 'delete'));
                elem.children('i').attr('class', 'icon-star');
            }
            else {
                elem.attr('href', url.replace('delete', 'create'));
                elem.children('i').attr('class', 'icon-star-empty');
            }
            $('.top-right').notify({
                message: { html: data.message },
                type: 'success'
            }).show();
        },
        complete: function (data) {
            elem.removeClass('load');
            elem.children('i').removeClass('icon-load');
        }
    });
});

