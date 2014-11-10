$(document).on('click', 'a[data-action=pm-answer]', function (e) {
    e.preventDefault();

    var elem = $(this)
        , url = elem.attr('href')
        , parent = elem.parents('.well')
        , parentId = parent.data('id')
        , answerForm = $('.answerForm:not(:last)');

    if (elem.data('activated') == '1') {
        elem.data('activated', 0);
        parent.nextAll('form').fadeOut('fast', function () {
            $(this).remove();
        });
        return false;
    }

    answerForm.prevAll('a[data="pm-answer"]').data('activated', 0);
    answerForm.fadeOut('fast', function () {
        $(this).remove();
    });

    $.ajax({
        type: 'get',
        dataType: 'json',
        url: pmsUrl,
        data: { parentId: parentId },
        cache: true,
        success: function (data) {
            $(data.data.view).insertAfter('#message-' + parentId);
            elem.data('activated', 1);
        }
    });
});


jQuery(function ($) {
    $(".fancybox").fancybox();

    $(".fancybox.youtube").click(function (e) {
        $.fancybox({
            'padding': 0,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'title': this.title,
            'width': 680,
            'height': 495,
            'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
            'type': 'swf',
            'swf': {
                'wmode': 'transparent',
                'allowfullscreen': 'true'
            }
        });

        return false;
    });
});

