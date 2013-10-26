$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
    var type = options.type || originalOptions.type;
    if (type.toLowerCase() == 'post') {
        var data = originalOptions.data;
        if (originalOptions.data !== undefined) {
            if (Object.prototype.toString.call(originalOptions.data) === '[object String]') {
                data = $.deparam(originalOptions.data); // see http://benalman.com/code/projects/jquery-bbq/examples/deparam/
            }
        }
        else {
            data = {};
        }

        if (options.data instanceof FormData) {
            options.data.append('csrf', $('meta[name="csrf"]').attr('content'));
        }
        else {
            options.data = $.param($.extend(data, { csrf: $('meta[name="csrf"]').attr('content') }));
        }
    }
});
$(document).ajaxError(function (event, request, settings) {
    if (request.status === 0 || request.readyState === 0) {
        return;
    }
    var errTxt = '';
    try {
        var data = eval("(" + request.responseText + ")");
        if (!data) {
            return;
        }
        if (data.data.errors) {
            $.each(data.data.errors, function (key, val) {
                errTxt += val + "\n";
            });
        } else {
            errTxt = data.message;
        }
    } catch (e) {
        errTxt = request.responseText;
    }

    $('.top-right').notify({
        message: { html: errTxt },
        fadeOut: {
            enabled: true,
            delay: 9000
        },
        type: 'error'
    }).show();
});

$(function () {
    $('body').append('<div class="notifications top-right"></div>');
    $('.loading').ajaxStart(function () {
        $(this).show();
    }).ajaxStop(function () {
            $(this).hide();
        });

    $(document).on('click', 'a[href="/user/login"]', function (e) {
        e.preventDefault();
        $("#loginModal").modal("show")
    });

    $(document).on('click', 'a:not([data-action])[href*="/delete"]', function (e) {
        e.preventDefault();
        console.log($(this));

        if (confirm('Are you sure?')) {
            $.yii.submitForm(this, $(this).attr('href'), {'csrf': $('meta[name="csrf"]').attr('content')});
        }
    });

    $(document).on('mouseup', '.yiiPager > li:not(.disabled, .active) > a', function (e) {
        $('html, body').animate({ scrollTop: 0 }, 150);
    });

    $(document).on('click', 'input[data-loading-text],button[data-loading-text],a[data-loading-text]', function (e) {
        $(this).button('loading');
    });

    (function ($) {

        //some closures
        var cont = ($.browser.msie && parseInt($.browser.version) <= 7) ? document.createElement("div") : null,
            mergeIfXhr = 0,
            resMap2Request = function (url) {
                if (!url.match(/\?/))
                    url += "?";
                return url + "&nlsc_map=" + $.nlsc.smap();
            };

        if (!$.nlsc)
            $.nlsc = {resMap: {}};

        $.nlsc.normUrl = function (url) {
            if (!url) return null;
            if (cont) {
                cont.innerHTML = "<a href=\"" + url + "\"></a>";
                //cont.innerHTML = cont.innerHTML;
                url = cont.firstChild.href;
                //console.log(url);
            }
            return url.replace(/\?*(_=\d+)?$/g, "");
        }
        $.nlsc.h = function (s) {
            var h = 0, i;
            for (i = 0; i < s.length; i++) {
                h = (((h << 5) - h) + s.charCodeAt(i)) & 1073741823;
            }
            return "" + h;
        }
        $.nlsc.fetchMap = function () {
            //fetching scripts from the DOM
            for (var url, i = 0, res = $(document).find("script[src]"); i < res.length; i++) {
                if (!(url = this.normUrl(res[i].src ? res[i].src : res[i].href))) continue;
                this.resMap[url] = $.nlsc.h(url);
            }//i
        }
        $.nlsc.smap = function () {
            var s = "[";
            for (var url in this.resMap)
                s += "\"" + this.resMap[url] + "\",";
            return s.replace(/,$/, "") + "]";
        }

        var c = {
            global: true,
            beforeSend: function (xhr, opt) {
                if (opt.dataType != "script") {
                    //hack: letting the server know what is already in the dom...
                    if (mergeIfXhr)
                        opt.url = resMap2Request(opt.url);
                    return true;
                }

                if (!$.nlsc.fetched) {
                    $.nlsc.fetched = 1;
                    $.nlsc.fetchMap();
                }//if

                var url = $.nlsc.normUrl(opt.url);
                if (!url) return true;
                if ($.nlsc.resMap[url]) return false;
                $.nlsc.resMap[url] = $.nlsc.h(url);
                return true;
            }//beforeSend
        };//c

        //removing "defer" attribute from IE scripts anyway
        if ($.browser.msie)
            c.dataFilter = function (data, type) {
                if (type && type != "html" && type != "text")
                    return data;
                return data.replace(/(<script[^>]+)defer(=[^\s>]*)?/ig, "$1");
            };

        $.ajaxSetup(c);

    })(jQuery);
});