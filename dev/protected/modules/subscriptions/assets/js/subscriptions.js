$(document).on('click', 'a[data-action=subscription]', function (e) {
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
            var icon = elem.children('i'), iconClass = icon.attr('class'), newClass, altTitle = elem.attr('data-alt-title'), title = elem.attr('data-original-title');

            if ( url.indexOf('/delete') != -1 ) {
                elem.attr('href', url.replace('delete', 'create'));

                if ( icon.attr('data-alt-class') ) {
                    newClass = icon.attr('data-alt-class');
                    icon.attr('class', newClass);
                    icon.attr('data-alt-class', iconClass);
                }
                else {
                    icon.attr('class', 'icon-eye-open');
                }
            }
            else {
                elem.attr('href', url.replace('create', 'delete'));

                if ( icon.attr('data-alt-class') ) {
                    newClass = icon.attr('data-alt-class');
                    icon.attr('class', newClass);
                    icon.attr('data-alt-class', iconClass);
                }
                else {
                    icon.attr('class', 'icon-eye-close');
                }
            }

            if ( altTitle ) {
                elem.tooltip('hide');
                elem.attr('data-original-title', altTitle);
                elem.attr('data-alt-title', title);
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

