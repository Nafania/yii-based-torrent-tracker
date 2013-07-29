$("#allAttributes").on('click', 'option', function (e) {
    var clon = $(this).clone();
    $(this).remove();
    $(clon).appendTo($("#categoryAttributes"));
});
$("#categoryAttributes").on('click', 'option', function (e) {
    var clon = $(this).clone();
    $(this).remove();
    $(clon).appendTo($("#allAttributes"));
});
$('#category-form').submit(function () {
    $("#categoryAttributes").find('option').attr('selected', 'selected');
});