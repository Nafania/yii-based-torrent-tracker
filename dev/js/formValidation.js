$.fn.EupdateSummary = function (form, messages) {
    var settings = $(form).data('settings'),
        content = '';
    if (settings.summaryID === undefined) {
        return;
    }
    if (messages) {
        $.each(messages, function (j, message) {
            content = content + '<li>' + message + '</li>';
            $('#' + j + '_em_').hide();
        });
    }
    $('#' + settings.summaryID).toggle(content !== '').find('ul').html(content);
};