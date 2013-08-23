$(document).on('click', 'a[data-action=fileList]', function (e) {
    e.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');
    var id = elem.data('id');
    var accordion = elem.parents('.accordion-heading').nextAll('#fileList' + id);
    if ( accordion.hasClass('in') || accordion.data('generated') ) {
        accordion.collapse('toggle');
        return false;
    }

    $.ajax({
        url: url,
        data: {id: id},
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
    location.hash && $(location.hash + ".collapse").collapse("show");
});

