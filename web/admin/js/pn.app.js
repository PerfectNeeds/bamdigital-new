$('body').on("click", ".delete-btn", function () {
    var id = $(this).data('delete');
    $('#del-form').attr('action', id);
});
$('body').on('click', 'a[href="#"]', function (e) {
    e.preventDefault();
});

if ($('.fab-menu-bottom-right').length > 0 && $('#fab-menu-affixed-demo-right').length > 0) {
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 40) {
            $('.fab-menu-bottom-right').addClass('reached-bottom');
        }
        else {
            $('.fab-menu-bottom-right').removeClass('reached-bottom');
        }
    });
// Right alignment
    $('#fab-menu-affixed-demo-right').affix({
        offset: {
            top: $('#fab-menu-affixed-demo-right').offset().top - 20
        }
    });
}
// Select with search
if ($('.select-search').length > 0) {
    $('.select-search').select2();
}

function successNotify(message) {
    new PNotify({
        text: message,
        addclass: 'alert bg-success alert-styled-right',
        type: 'success'
    });
}
function errorNotify(message) {
    new PNotify({
        text: message,
        addclass: 'alert bg-danger alert-styled-right',
        type: 'error'
    });
}