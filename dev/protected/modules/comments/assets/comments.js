$(document).on('click', '.commentReply', function (e) {
    e.preventDefault();

    var elem = $(this);
    var modelName = $('#Comment_modelName').val();
    var modelId = $('#Comment_modelId').val();
    var parentId = elem.parents('.commentContainer').data('id');
    var answerBlock = $('.answerBlock:not(:last)');

    if (elem.data('activated') == '1') {
        elem.data('activated', 0);
        elem.nextAll('.answerBlock').fadeOut('fast', function () {
            $(this).remove();
        });
        return false;
    }

    answerBlock.prevAll('.commentReply').data('activated', 0);
    answerBlock.remove();

    $.ajax({
        type: 'get',
        dataType: 'json',
        url: commentsUrl,
        data: {modelName: modelName, modelId: modelId, parentId: parentId },
        cache: false,
        success: function (data) {
            $(data.data.view).insertAfter(elem);
            elem.data('activated', '1');
        }
    });
});

$(document).on('click', 'a[data-action=updateComment]', function (e) {
    e.preventDefault();

    var elem = $(this);

    elem.children('i').addClass('icon-load');

    $.ajax({
        type: 'get',
        dataType: 'json',
        url: elem.attr('href'),
        data: {'Comment[id]': elem.data('id')},
        success: function (data) {
            if (data.data.id) {

            }
            else {
                var comment = elem.parents('.comment:first');
                comment.find('.commentText').html(data.data.view);
            }
        },
        complete: function () {
            elem.children('i').removeClass('icon-load');
        }
    });
});
$(document).on('click', 'a[data-action=deleteComment]', function (e) {
    e.preventDefault();

    var elem = $(this);

    elem.children('i').addClass('icon-load');

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: elem.attr('href'),
        data: {id: elem.data('id')},
        success: function (data) {
            elem.parents('.commentContainer:first').remove();
            $('.top-right').notify({
                message: { html: data.message },
                fadeOut: {
                    enabled: true,
                    delay: 3000
                },
                type: 'success'
            }).show();
        },
        complete: function () {
            elem.children('i').removeClass('icon-load');
        }
    });
});

$(document).on('mouseenter', '.commentText', function (e) {
    $(this).removeClass('commentTextGradient');
    if ($(this).hasClass('rating0')) {
        return true;
    }
    var opacity = $(this).css('opacity');
    $(this).attr('data-opacity', opacity);
    $(this).stop();
    $(this).fadeTo('slow', 1);
});
$(document).on('mouseleave', '.commentText', function (e) {
    $(this).addClass('commentTextGradient');
    if ($(this).hasClass('rating0')) {
        return true;
    }
    $(this).stop();
    $(this).fadeTo('slow', $(this).data('opacity'));
});
$(document).on('mouseenter', '.comment', function (e) {
    $(this).find('.commentOptions:first a').fadeIn('fast');
});
$(document).on('mouseleave', '.comment', function (e) {
    $(this).find('.commentOptions:first a').fadeOut('fast');
});
$(document).on('mousedown', '.redactor_btn_quote', function (e) {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    }
    else {
        text = document.selection.createRange().text;
    }
    $(this).data('selection', text);
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

    /*$('.answerForm').yiiactiveform({
        'afterValidate': function (form, data, hasError) {
            var button = form.find("input[type=submit]");
            if (!hasError) {
                $.ajax({
                    type: "POST",
                    url: form.attr("action"),
                    data: form.serialize(),
                    dataType: "json",
                    success: function (data) {
                        var Comment_id = $(form).find("#Comment_id");
                        if (Comment_id.val()) {
                            $(form).parents(".commentText:first").html($(data.data.view).find(".commentText").html());
                        }
                        else {
                            $("#Comment_text").redactor("set", "");
                            $(form).prevAll(".commentReply").data("activated", 0);
                            if (data.data.parentId) {
                                $(form).remove();
                                $(data.data.view).appendTo("#comment-" + data.data.parentId + " > div.media-body");
                            }
                            else {
                                if ($(".commentContainer").length) {
                                    $(data.data.view).insertAfter(".commentsBlock > .commentContainer:last-child");
                                }
                                else {
                                    $(data.data.view).appendTo(".commentsBlock");
                                }
                            }
                            $("html, body").animate({scrollTop: $("#comment-" + data.data.id).position().top }, 100);
                        }
                    },
                    complete: function () {
                        button.button("reset");
                    }
                });
            }
            else {
                button.button("reset");
            }

        }
    });*/

    /*$('.answerForm').yiiactiveform({'validateOnSubmit': true, 'afterValidate': function (form, data, hasError) {
     if (!hasError) {
     $.ajax({
     type: "POST",
     url: $(form).attr('action'),
     data: form.serialize(),
     dataType: "json",
     success: function (data) {
     $("#Comment_text").redactor("set", "");
     $(form).prevAll(".commentReply").data("activated", 0);
     if (data.data.parentId) {
     $(form).remove();
     $(data.data.view).appendTo("#comment-" + data.data.parentId + " > div.comment");
     }
     else {
     if ($(".commentContainer").length) {
     $(data.data.view).insertAfter(".commentsBlock > .commentContainer:last-child");
     }
     else {
     $(data.data.view).appendTo(".commentsBlock");
     }
     }
     }

     });
     }
     }, 'attributes': [
     {'id': 'Comment_text', 'inputID': 'Comment_text', 'errorID': 'Comment_text_em_', 'model': 'Comment', 'name': 'text', 'enableAjaxValidation': true, 'clientValidation': function (value, messages, attribute) {

     if (jQuery.trim(value) == '') {
     messages.push("\u041d\u0435\u043e\u0431\u0445\u043e\u0434\u0438\u043c\u043e \u0437\u0430\u043f\u043e\u043b\u043d\u0438\u0442\u044c \u043f\u043e\u043b\u0435 \u00abText\u00bb.");
     }

     }}
     ], 'errorCss': 'error'});
     /*$('.answerForm').yiiactiveform({
     'attributes': [
     {'id': 'Comment_text', 'inputID': 'Comment_text', 'errorID': 'Comment_text_em_', 'model': 'Comment', 'name': 'text',
     'enableAjaxValidation': true,
     'clientValidation': function (value, messages, attribute) {
     if (jQuery.trim(value) == '') {
     messages.push("\u041d\u0435\u043e\u0431\u0445\u043e\u0434\u0438\u043c\u043e \u0437\u0430\u043f\u043e\u043b\u043d\u0438\u0442\u044c \u043f\u043e\u043b\u0435 \u00abText\u00bb.");
     }
     }
     }
     ],
     'errorCss': 'error',
     'validateOnSubmit': true,
     'afterValidate': function (form, data, hasError) {
     if (!hasError) {
     $.ajax({
     type: "POST",
     url: $(form).attr('action'),
     data: form.serialize(),
     dataType: "json",
     success: function (data) {
     $("#Comment_text").redactor("set", "");
     $(form).prevAll(".commentReply").data("activated", 0);
     if (data.data.parentId) {
     $(form).remove();
     $(data.data.view).appendTo("#comment-" + data.data.parentId + " > div.comment");
     }
     else {
     if ($(".commentContainer").length) {
     $(data.data.view).insertAfter(".commentsBlock > .commentContainer:last-child");
     }
     else {
     $(data.data.view).appendTo(".commentsBlock");
     }
     }
     }
     });
     }
     }}
     );*/
});