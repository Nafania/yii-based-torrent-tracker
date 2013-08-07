$(document).on('click', '.commentReply', function (e) {
    e.preventDefault();

    var elem = $(this);
    var modelName = $('#Comment_modelName').val();
    var modelId = $('#Comment_modelId').val();
    var parentId = elem.parents('.commentContainer').data('id');
    var answerBlock = $('.answerBlock:not(:last)');

    if (elem.data('activated') == '1') {
        elem.data('activated', 0);
        elem.nextAll('.answerBlock').fadeOut('slow');
        return false;
    }

    answerBlock.prevAll('.commentReply').data('activated', 0);
    answerBlock.fadeOut('slow', function () {
        $(this).remove();
    });

    $.ajax({
        type: 'get',
        dataType: 'json',
        url: commentsUrl,
        data: {modelName: modelName, modelId: modelId, parentId: parentId, csrf: $('meta[name="csrf"]').attr('content') },
        cache: true,
        success: function (data) {
            $(data.data.view).insertAfter(elem);
            elem.data('activated', 1);
        }
    });
});
$(document).on('mouseenter', '.commentText', function (e) {
    var opacity = $(this).css('opacity');
    $(this).attr('data-opacity', opacity);
    $(this).stop();
    $(this).fadeTo('slow', 1);
});
$(document).on('mouseleave', '.commentText', function (e) {
    $(this).stop();
    $(this).fadeTo('slow', $(this).data('opacity'));
});
$(document).ready(function () {
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

    $('a[data-comments-for]').click(function (e) {
        e.preventDefault();

        var tid = $(this).data('comments-for');
        var tidDiv = $('.media[data-comments-for=' + tid + ']');
        var length = $('a[data-comments-for]').length;

        if (!tidDiv.length) {
            return false;
        }

        if ($(this).data('filtered')) {
            $(this).children('i').removeClass('icon-white');
            tidDiv.fadeOut('slow');

            $(this).data('filtered', false);
        }
        else {
            $(this).children('i').addClass('icon-white');
            tidDiv.fadeIn('slow');

            $(this).data('filtered', true);
        }

        var _i = 0;
        $('a[data-comments-for]').each(function () {
            var _tid = $(this).data('comments-for');

            if (!$(this).data('filtered')) {
                $('.media[data-comments-for=' + _tid + ']').fadeOut('slow');
                ++_i;
            }
        });

        if (_i == length) {
            $('.media').fadeIn('slow');
        }
        else {
            $('.media[data-comments-for=""]').fadeOut('slow');
        }
    });

});
