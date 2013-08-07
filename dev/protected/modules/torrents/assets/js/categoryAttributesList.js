$("#allTorrentsNameRules").on('click', 'option', function (e) {
    var clon = $(this).clone();
    $(this).remove();
    $(clon).appendTo($("#torrentsNameRules"));
});
$("#torrentsNameRules").on('click', 'option', function (e) {
    var clon = $(this).clone();
    $(this).remove();
    $(clon).appendTo($("#allTorrentsNameRules"));
});
$('#category-form').submit(function () {
    $("#torrentsNameRules").find('option').attr('selected', 'selected');
});