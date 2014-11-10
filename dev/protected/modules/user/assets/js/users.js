$(document).on('click', 'a[data-action=social-delete]', function (e) {
    e.preventDefault();

    var elem = $(this)
        , url = elem.attr('href');

    $.ajax({
        type: 'get',
        dataType: 'json',
        url: url,
        cache: true,
        success: function (data) {
            if ( elem.parents("ul").children('li').length == 1 ) {
                var ul = elem.parents("ul");
                ul.prevAll('h4').remove();
                ul.remove();
            }
            else {
                elem.parents("li").remove();
            }
            $(".top-right").notify({
                message: { html: data.message },
                fadeOut: {
                    enabled: true,
                    delay: 9000
                },
                type: "success"
            }).show();
        }
    });
});