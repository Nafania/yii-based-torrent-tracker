$(document).on('click', 'a[data-action=event]', function (e) {
    var elem = $(this);

    $.ajax({
        url: elem.data('eventurl'),
        data: {id: elem.data('id')},
        dataType: 'json',
        type: 'POST'
    });
});