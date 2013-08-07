$(document).on('click', 'a[data-action=rating]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');

    $.ajax({
        url: url,
        data: {modelName: elem.data('model'), modelId: elem.data('id'), state: elem.data('state')},
        dataType: 'json',
        type: 'POST',
        success: function (data) {
            var badge = elem.prevAll('.badge');
            //console.log(badge);
            if (typeof data.data == 'undefined' || typeof data.data.rating == 'undefined') {
                return false;
            }
            var rating = data.data.rating;
            badge.attr('class', 'badge');
            badge.html(rating);
            if (rating > 0) {
                badge.addClass('badge-success');
            }
            else if (rating < 0) {
                badge.addClass('badge-important');
            }
        }

    });
});

