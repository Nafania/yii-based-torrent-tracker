$(document).on('click', 'a[data-action=filesList]', function (e) {
    e.preventDefault();

    var elem = $(this),
        url = elem.attr('href'),
        id = elem.data('id'),
        modelName = elem.data('model');

    var accordion = elem.parents('.accordion-heading').nextAll('#filesList' + id);
    if ( accordion.hasClass('in') || accordion.data('generated') ) {
        accordion.collapse('toggle');
        return false;
    }

    $.ajax({
        url: url,
        data: {id: id, modelName: modelName},
        dataType: 'html',
        type: 'GET',
        cache: true,
        success: function (data) {
            accordion.children('.accordion-inner').html(data);
            accordion.data('generated', 1);
            accordion.collapse("show");
        }

    });
});
jQuery(function ($) {
    $(".fancybox").fancybox();
});

