$(function () {
    $('.treeview li').hide();
    $('.treeview li:first').show();
    $('.treeview li').on('click', function (e) {
        var children = $(this).find('> ul > li');
        if (children.is(":visible")) children.hide('fast');
        else children.show('fast');
        e.stopPropagation();
    });
});